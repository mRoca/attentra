<?php

namespace Attentra\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PublicRestController extends Controller
{

    public function getPingAction()
    {
        return ['ping' => true];
    }

} 
