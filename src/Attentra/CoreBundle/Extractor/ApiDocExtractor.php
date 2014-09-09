<?php

namespace Attentra\CoreBundle\Extractor;

use Nelmio\ApiDocBundle\Extractor\ApiDocExtractor as Base;
use Symfony\Component\Routing\Route;

use Doctrine\Common\Annotations\Reader;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Parser\PostParserInterface;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Nelmio\ApiDocBundle\Util\DocCommentExtractor;

/**
 * Extend of Nelmio\ApiDocBundle\Extractor\ApiDocExtractor to give the route name to the input / output parsers.
 * This parameter permit to get the entities and form classes from config files when using action methods inheritance (GenericRestController)
 *
 * TODO create a PR to Nelmio\ApiDocBundle
 */
class ApiDocExtractor extends Base
{
    /**
     * @var DocCommentExtractor
     */
    private $commentExtractor;

    public function __construct(ContainerInterface $container, RouterInterface $router, Reader $reader, DocCommentExtractor $commentExtractor, array $handlers)
    {
        $this->container        = $container;
        $this->router           = $router;
        $this->reader           = $reader;
        $this->commentExtractor = $commentExtractor;
        $this->handlers         = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    protected function extractData(ApiDoc $annotation, Route $route, \ReflectionMethod $method)
    {
        // create a new annotation
        $annotation = clone $annotation;

        // doc
        $annotation->setDocumentation($this->commentExtractor->getDocCommentText($method));

        // parse annotations
        $this->parseAnnotations($annotation, $route, $method);

        // route
        $annotation->setRoute($route);

        // input (populates 'parameters' for the formatters)
        if (null !== $input = $annotation->getInput()) {
            $parameters      = array();
            $normalizedInput = $this->normalizeClassParameter($input, $route->getDefault('_controller')); //Updated

            $supportedParsers = array();
            foreach ($this->getParsers($normalizedInput) as $parser) {
                if ($parser->supports($normalizedInput)) {
                    $supportedParsers[] = $parser;
                    $parameters         = $this->mergeParameters($parameters, $parser->parse($normalizedInput));
                }
            }

            foreach ($supportedParsers as $parser) {
                if ($parser instanceof PostParserInterface) {
                    $parameters = $this->mergeParameters(
                        $parameters,
                        $parser->postParse($normalizedInput, $parameters)
                    );
                }
            }

            $parameters = $this->clearClasses($parameters);
            $parameters = $this->generateHumanReadableTypes($parameters);

            if ('PUT' === $method) {
                // All parameters are optional with PUT (update)
                array_walk($parameters, function ($val, $key) use (&$data) {
                    $parameters[$key]['required'] = false;
                });
            }

            $annotation->setParameters($parameters);
        }

        // output (populates 'response' for the formatters)
        if (null !== $output = $annotation->getOutput()) {
            $response         = array();
            $supportedParsers = array();

            $normalizedOutput = $this->normalizeClassParameter($output, $route->getDefault('_controller')); //Updated

            foreach ($this->getParsers($normalizedOutput) as $parser) {
                if ($parser->supports($normalizedOutput)) {
                    $supportedParsers[] = $parser;
                    $response           = $this->mergeParameters($response, $parser->parse($normalizedOutput));
                }
            }

            foreach ($supportedParsers as $parser) {
                if ($parser instanceof PostParserInterface) {
                    $mp       = $parser->postParse($normalizedOutput, $response);
                    $response = $this->mergeParameters($response, $mp);
                }
            }

            $response = $this->clearClasses($response);
            $response = $this->generateHumanReadableTypes($response);

            $annotation->setResponse($response);
            $annotation->setResponseForStatusCode($response, $normalizedOutput, 200);
        }

        if (count($annotation->getResponseMap()) > 0) {

            foreach ($annotation->getResponseMap() as $code => $modelName) {

                if ('200' === (string)$code && isset($modelName['type']) && isset($modelName['model'])) {
                    /*
                     * Model was already parsed as the default `output` for this ApiDoc.
                     */
                    continue;
                }

                $normalizedModel = $this->normalizeClassParameter($modelName, $route->getDefault('_controller')); //Updated

                $parameters       = array();
                $supportedParsers = array();
                foreach ($this->getParsers($normalizedModel) as $parser) {
                    if ($parser->supports($normalizedModel)) {
                        $supportedParsers[] = $parser;
                        $parameters         = $this->mergeParameters($parameters, $parser->parse($normalizedModel));
                    }
                }

                foreach ($supportedParsers as $parser) {
                    if ($parser instanceof PostParserInterface) {
                        $mp         = $parser->postParse($normalizedModel, $parameters);
                        $parameters = $this->mergeParameters($parameters, $mp);
                    }
                }

                $parameters = $this->clearClasses($parameters);
                $parameters = $this->generateHumanReadableTypes($parameters);

                $annotation->setResponseForStatusCode($parameters, $normalizedModel, $code);

            }

        }

        return $annotation;
    }

    /**
     *  Updated to return the route information
     *
     * @param $input
     * @param string $controllerAndMethod
     * @return array
     */
    protected function normalizeClassParameter($input, $controllerAndMethod = '')
    {
        $defaults = array(
            'class'      => '',
            'groups'     => array(),
            'controller' => $controllerAndMethod
        );

        // normalize strings
        if (is_string($input)) {
            $input = array('class' => $input);
        }

        // normalize groups
        if (isset($input['groups']) && is_string($input['groups'])) {
            $input['groups'] = array_map('trim', explode(',', $input['groups']));
        }

        return array_merge($defaults, $input);
    }

    private function getParsers(array $parameters)
    {
        if (isset($parameters['parsers'])) {
            $parsers = array();
            foreach ($this->parsers as $parser) {
                if (in_array(get_class($parser), $parameters['parsers'])) {
                    $parsers[] = $parser;
                }
            }
        } else {
            $parsers = $this->parsers;
        }

        return $parsers;
    }


} 
