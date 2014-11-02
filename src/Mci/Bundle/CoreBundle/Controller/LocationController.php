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
        $districtByDivision = array();
        if($id){
            $districts =  $this->getJsonData('district.json');
          //  $districts = json_decode($this->remove_utf8_bom($data));

            foreach ($districts as $key => $value) {

                if ($value['division_id'] == $id) {
                    $districtByDivision[$key]['code'] = $value['code'];
                    $districtByDivision[$key] ['name'] = $value['name'];
                    $districtByDivision[$key]['id'] = $value['id'];
                }
            }
            return new Response(json_encode($districtByDivision));
        }else{
            return new Response(json_encode($districtByDivision));
        }

    }

    public function upazillaAction($id)
    {
        $upazillaByDistrict = array();

        if ($id) {
            $upazillas =  $this->getJsonData('upazilla.json');

            foreach ($upazillas as $key => $value) {

                if ($value['district_id'] == $id) {
                    $upazillaByDistrict[$key]['code'] = $value['code'];
                    $upazillaByDistrict[$key] ['name'] = $value['name'];
                    $upazillaByDistrict[$key]['id'] = $value['id'];
                }
            }

            return new Response(json_encode($upazillaByDistrict));

        } else {
            return new Response(json_encode($upazillaByDistrict));
        }
    }


    private function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }
}
