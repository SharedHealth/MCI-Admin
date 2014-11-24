<?php

namespace Mci\Bundle\PatientBundle\Services;
use Symfony\Component\HttpFoundation\Response;

class Location {
   public $container;

   public function __construct($servicecontainer){
       $this->container = $servicecontainer;
   }

   public function getLocations($url = ''){
        try{
            $client = $this->container->get('mci_patient.client');
            $client->setDefaultOption('headers',
                array(
                    'X-Auth-Token' => $this->container->getParameter('location_auth_key'),
                    'Content-Type'=>'application/json'
                )
            );

            $request = $client->get($this->container->getParameter('location_api_end_point').'/'.$url);
            $response = $request->send();

            return $response->getBody();

        } catch(RequestException $e){
            echo  $e->getMessage();
        }
    }


    public function getDistrict($id){
        $districtByDivision = array();
        if($id){
            $districts =  $this->getJsonData('district.json');
            //  $districts = json_decode($this->remove_utf8_bom($data));

            foreach ($districts as $key => $value) {

                if ($value['division_id'] == $id) {
                    $districtByDivision[$key]['code'] = str_pad($value['code'],2,0,STR_PAD_LEFT);
                    $districtByDivision[$key] ['name'] = $value['name'];
                    $districtByDivision[$key]['id'] = $value['id'];
                }
            }
            return $districtByDivision;
        }else{
            return $districtByDivision;
        }
    }

    public function getUpazilla($id){
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

            return $upazillaByDistrict;

        } else {
            return $upazillaByDistrict;
        }
    }

    public function getAllDivision(){
        $divisions =  $this->getJsonData('division.json');

        foreach($divisions as $key=>$val){
            $filterDivisions[$val['code'].'_'.$val['id']] = $val['name'];
        }

        return $filterDivisions;
    }


    public function getAllDistrict(){
        $districts =  $this->getJsonData('district.json');

        foreach($districts as $key=>$val){
            $filterDistricts[$val['code'].'_'.$val['id']] = $val['name'];
        }

        return $filterDistricts;
    }

    public function getAllUpazilla(){
        $upazillas =  $this->getJsonData('upazilla.json');

        foreach($upazillas as $key=>$val){
            $filterUpazillas[$val['code']] = $val['name'];
        }

        return $filterUpazillas;
    }


    private function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }

}
