<?php
namespace Mci\Bundle\PatientBundle\Twig;



class MciExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('gender', array($this, 'genderFilter')),
            new \Twig_SimpleFilter('occupation', array($this, 'occupationFilter')),
            new \Twig_SimpleFilter('edulevel', array($this, 'eduLevelFilter')),
            new \Twig_SimpleFilter('bloodgroup', array($this, 'bloodGroupFilter')),
            new \Twig_SimpleFilter('religion', array($this, 'religionFilter')),
            new \Twig_SimpleFilter('disability', array($this, 'disabilityFilter')),
            new \Twig_SimpleFilter('division', array($this, 'divisionFilter')),
            new \Twig_SimpleFilter('district', array($this, 'districtFilter')),
            new \Twig_SimpleFilter('upazila', array($this, 'upazilaFilter')),
            new \Twig_SimpleFilter('countrycode', array($this, 'countryCodeFilter')),
            new \Twig_SimpleFilter('maritalStatus', array($this, 'maritalStatusFilter')),
            new \Twig_SimpleFilter('relation', array($this, 'relationFilter')),
            new \Twig_SimpleFilter('divisionCodeToId', array($this, 'divisionCodeConvertFilter'))
        );
    }

    public function genderFilter($number)
    {
        $gender =  $this->getJsonData('gender.json');
        return isset($gender[$number])?$gender[$number]:'';
    }

    public function occupationFilter($number)
    {
        $occupation = $this->getJsonData('occupation.json');
        return isset($occupation[$number])?$occupation[$number]:'';
    }

    public function eduLevelFilter($number)
    {
        $eduLevel = $this->getJsonData('eduLevel.json');
        return isset($eduLevel[$number])?$eduLevel[$number]:'';
    }

    public function bloodGroupFilter($number)
    {
        $bloodGroup = $this->getJsonData('bloodGroup.json');
        return isset($bloodGroup[$number])?$bloodGroup[$number]:'';
    }

    public function religionFilter($number){
        $religion = $this->getJsonData('religion.json');
        return isset($religion[$number])?$religion[$number]:'';
    }
    public function disabilityFilter($number)
    {
        $disability = $this->getJsonData('disability.json');
        return isset($disability[$number])?$disability[$number]:'';
    }

    public function divisionFilter($number)
    {
        $data = $this->getJsonData('division.json');
        $division = $this->getFilterData($data);

        return isset($division[$number])?$division[$number]:'';
    }

    public function districtFilter($number)
    {
        $data = $this->getJsonData('district.json');
        $district = $this->getFilterData($data);
        return isset($district[$number])?$district[$number]:'';
    }

    public function upazilaFilter( $district_code='',$upazila_code='')
    {

        $data = $this->getJsonData('upazila-single.json');
        return $data[$upazila_code][$district_code];
    }

    public function countryCodeFilter($number)
        {
            $country = $this->getJsonData('countryCode.json');
            return isset($country[$number])?$country[$number]:'';
        }
    public function maritalStatusFilter($number)
        {
            $maritalStatus = $this->getJsonData('relationStat.json');

            return isset($maritalStatus[$number])?$maritalStatus[$number]:'';
        }
    public function relationFilter($number)
        {
            $relation = $this->getJsonData('relation.json');
            return isset($relation[$number])?$relation[$number]:'';
        }

    public function getName()
    {
        return 'mci_extension';
    }

    private function getJsonData($fileName)
    {
       $filePath =  'assets/json/'.$fileName;
       return  json_decode(file_get_contents($filePath), true);
    }

    private function getFilterData($data){
        $FilterArray = array();
        foreach ($data as $value) {
             $code = str_pad($value['code'],2,'0',STR_PAD_LEFT);
             $FilterArray[$code] = $value['name'];
         }
        return $FilterArray;
    }


    public function divisionCodeConvertFilter($code){
        $divisions =  $this->getJsonData('division.json');
        foreach($divisions as $key=>$val){
            $divisionCode = str_pad($val['code'],2,0,STR_PAD_LEFT);
            if($divisionCode == $code){
                return $val['id'];
            }
        }
    }


}
?>
