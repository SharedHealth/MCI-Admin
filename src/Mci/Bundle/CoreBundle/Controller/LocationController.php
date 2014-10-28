<?php

namespace Mci\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{

    public function divisionAction()
    {
        $client = $this->get('mci.location');
        $data =  $client->getLocations('api/1.0/locations/divisions/list');
        return new Response($data);
    }

    public function districtAction($id)
    {
        $client = $this->get('mci.location');
        $data =  $client->getLocations('api/1.0/locations/districts/list');
        $districts = json_decode($this->remove_utf8_bom($data));

        foreach ($districts as $key => $value) {

            if ($value->division_id == $id) {
                $districtByDivision[$key]['code'] = $value->code;
                $districtByDivision[$key] ['name'] = $value->name;
                $districtByDivision[$key]['id'] = $value->id;
            }
        }
        return new Response(json_encode($districtByDivision));
    }

    public function upazillaAction($id)
    {
        $client = $this->get('mci.location');
        $data =  $client->getLocations('api/1.0/locations/upazilas/list');
        $upazillas = json_decode($this->remove_utf8_bom($data));

        foreach ($upazillas as $key => $value) {

            if ($value->district_id == $id) {
                $upazillaByDistrict[$key]['code'] = $value->code;
                $upazillaByDistrict[$key] ['name'] = $value->name;
                $upazillaByDistrict[$key]['id'] = $value->id;
            }
        }
        return new Response(json_encode($upazillaByDistrict));
    }

    private function remove_utf8_bom($text)
    {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
}
