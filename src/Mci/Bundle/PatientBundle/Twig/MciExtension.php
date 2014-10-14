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
        );
    }

    public function genderFilter($number)
    {
        $gender =  $this->getJsonData('gender.json');
        return $gender[$number];
    }

    public function occupationFilter($number)
    {
        $occupation = $this->getJsonData('occupation.json');
        return $occupation[$number];
    }

    public function eduLevelFilter($number)
    {
        $eduLevel = $this->getJsonData('eduLevel.json');
        return $eduLevel[$number];
    }

    public function bloodGroupFilter($number){
        $bloodGroup = $this->getJsonData('bloodGroup.json');
        return $bloodGroup[$number];
    }

    public function religionFilter($number){
        $religion = $this->getJsonData('religion.json');
        return $religion[$number];
    }
    public function disabilityFilter($number){
        $disability = $this->getJsonData('disability.json');
        return $disability[$number];
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
}
?>
