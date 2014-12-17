<?php

namespace Mci\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{

    public function indexAction($code = NULL)
    {
      $result = $this->container->get('mci.location')->getLocation($code);
      return  new Response(json_encode($result));
    }
}
