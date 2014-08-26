<?php

namespace Attentra\ApiBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ResourceRestController extends Controller
{

    public function getResourcesAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AttentraResourceBundle:Resource')->findBy([], ['name' => 'asc']);
    }
} 
