<?php

namespace Mci\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{

    public function indexAction($code = NULL)
    {
        return  new JsonResponse($this->container->get('mci.location')->getChildLocations($code));
    }
}
