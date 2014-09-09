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
        $item['class'] = 'Attentra\ResourceBundle\Entity\Resource';
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

        if ($item['attentra_type'] === 'entity') {
            $params = array(
                'class'  => $controller->getHandler()->getEntityClass(),
                'groups' => $item['groups']
            );

            $parsers = array('nelmio_api_doc.parser.jms_metadata_parser', 'nelmio_api_doc.parser.validation_parser');
            foreach ($parsers as $parserName) {
                /** @var ParserInterface $parser */
                $parser = $this->container->get($parserName);
                if ($parser->supports($params)) {
                    return $parser->parse($params);
                }
            }

        } elseif ($item['attentra_type'] === 'form_type') {

            return $this->container->get('nelmio_api_doc.parser.form_type_parser')->parse(array(
                'class'  => $controller->getHandler()->getFormTypeClass(),
                'groups' => $item['groups']
            ));

        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function postParse(array $item, array $parameters)
    {
        if ($item['attentra_type'] === 'entity') {

            return $this->container->get('nelmio_api_doc.parser.jms_metadata_parser')->postParse(array(
                'class'  => $this->getController($item['controller'])->getHandler()->getEntityClass(),
                'groups' => $item['groups']
            ), $parameters);

        } elseif ($item['attentra_type'] === 'form_type') {

            return $this->container->get('nelmio_api_doc.parser.validation_parser')->postParse(array(
                'class'  => $this->getController($item['controller'])->getHandler()->getFormTypeClass(),
                'groups' => $item['groups']
            ), $parameters);

        }

        return $parameters;
    }

    protected function getControllerName($serviceName)
    {
        list($serviceName, $methodName) = explode(':', $serviceName);
        return $serviceName;
    }

    protected function getController($serviceName)
    {
        return $this->container->get($this->getControllerName($serviceName));
    }
} 
