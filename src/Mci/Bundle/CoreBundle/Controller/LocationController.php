<?php

namespace Mci\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{

    public function divisionAction()
    {

        $data =  $this->getJsonData('division.json');
        return new Response($data);
    }

    public function districtAction($id)
    {
      $districts = $this->container->get('mci.location')->getDistrict($id);
      return  new Response(json_encode($districts));

    }

    public function upazillaAction($id)
    {
        $upazillas = $this->container->get('mci.location')->getUpazilla($id);
        return  new Response(json_encode($upazillas));
    }


    private function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }
}
