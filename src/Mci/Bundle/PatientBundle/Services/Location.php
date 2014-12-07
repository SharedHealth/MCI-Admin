<?php

namespace Mci\Bundle\PatientBundle\Services;
use Symfony\Component\HttpFoundation\Response;

class Location {
   public $container;

   public function __construct($servicecontainer){
       $this->container = $servicecontainer;
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
        }
        return $districtByDivision;
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
        }
        return $upazillaByDistrict;
    }

    public function getAllDivision(){
        $divisions =  $this->getJsonData('division.json');

        foreach($divisions as $key=>$val){
            $filterDivisions[$val['code']] = $val['name'];
        }

        return $filterDivisions;
    }


    public function getAllDistrict($id){
        $districts =  $this->getJsonData('district.json');
        $filterDistricts = array();
        foreach($districts as $key=>$val){
            if ($val['division_id'] == $id) {
                $filterDistricts[str_pad($val['code'], 2, 0, STR_PAD_LEFT)] = $val['name'];
            }
        }

        return $filterDistricts;
    }

    public function getAllUpazilla($id){
        $upazillaByDistrict = array();

        if ($id) {
            $upazillas =  $this->getJsonData('upazilla.json');

            foreach ($upazillas as $key => $value) {

                if ($value['district_id'] == $id) {
                    $upazillaByDistrict[$value['code']] = $value['name'] ;

                }
            }

        }
        return $upazillaByDistrict;
    }


    public function getDivisionId($code){
        $divisions =  $this->getJsonData('division.json');
        foreach($divisions as $key=>$val){
            $divisionCode = str_pad($val['code'],2,0,STR_PAD_LEFT);
            if($divisionCode == $code){
               return $val['id'];
            }
        }
    }

    public function getDistictId($code){
        $districts =  $this->getJsonData('district.json');
        foreach($districts as $key=>$val){
            $disctrictCode = str_pad($val['code'],2,0,STR_PAD_LEFT);
            if($disctrictCode == $code){
                return $val['id'];
            }
        }

    }


    private function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }

     public function getLocationId($data, $code){
        foreach($data as $value){
            if($value['code'] == $code ){
                return $value['id'];
            }
        }
    }

}
