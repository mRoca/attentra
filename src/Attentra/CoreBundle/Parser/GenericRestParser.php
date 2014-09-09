<?php

namespace Attentra\CoreBundle\Parser;

use Attentra\CoreBundle\Controller\GenericRestController;
use Nelmio\ApiDocBundle\Parser\ParserInterface;
use Nelmio\ApiDocBundle\Parser\PostParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenericRestParser implements ParserInterface, PostParserInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(array $item)
    {
        if (isset($item['controller'], $item['attentra_type']) && $item['controller']) {
            return $this->getController($item['controller']) instanceof GenericRestController;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $item)
    {
        /** @var GenericRestController $controller */
        $controller = $this->getController($item['controller']);

        if ($item['attentra_type'] === 'entity' || $item['attentra_type'] === 'entity_collection') {

            $params = array_merge($item, $this->getEntityParameters($controller));

            $parsers = array('nelmio_api_doc.parser.jms_metadata_parser', 'nelmio_api_doc.parser.validation_parser');
            foreach ($parsers as $parserName) {
                /** @var ParserInterface $parser */
                $parser = $this->container->get($parserName);
                if ($parser->supports($params)) {
                    return $parser->parse($params);
                }
            }

        } elseif ($item['attentra_type'] === 'form_type') {

            return $this->container->get('nelmio_api_doc.parser.form_type_parser')->parse(array_merge($item, $this->getFormTypeParameters($controller)));

        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function postParse(array $item, array $parameters)
    {
        /** @var GenericRestController $controller */
        $controller = $this->getController($item['controller']);

        if ($item['attentra_type'] === 'entity_collection') {

            return $this->container->get('nelmio_api_doc.parser.collection_parser')->postParse(
                array_merge($item, $this->getEntityParameters($controller)),
                $this->container->get('nelmio_api_doc.parser.jms_metadata_parser')->postParse(array_merge($item, $this->getEntityParameters($controller)), $parameters)
            );

        } else if ($item['attentra_type'] === 'entity') {

            return $this->container->get('nelmio_api_doc.parser.jms_metadata_parser')->postParse(array_merge($item, $this->getEntityParameters($controller)), $parameters);

        } elseif ($item['attentra_type'] === 'form_type') {

            return $this->container->get('nelmio_api_doc.parser.validation_parser')->postParse(array_merge($item, $this->getFormTypeParameters($controller)), $parameters);

        }

        return $parameters;
    }

    protected function getController($serviceNameAndMethod)
    {
        list($serviceName, $methodName) = explode(':', $serviceNameAndMethod);

        return $this->container->get($serviceName);
    }

    protected function getFormTypeParameters(GenericRestController $controller)
    {
        return array(
            'class' => $controller->getHandler()->getFormTypeClass(),
            'name'  => '' //Permit to hide the form name in input fields name, see Nelmio\ApiDocBundle\Parser\FormTypeParser
        );
    }

    protected function getEntityParameters(GenericRestController $controller)
    {
        return array(
            'class' => $controller->getHandler()->getEntityClass(),
        );
    }
} 
