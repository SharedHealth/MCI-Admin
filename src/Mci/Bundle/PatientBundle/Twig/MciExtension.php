<?php
namespace Mci\Bundle\PatientBundle\Twig;


use Mci\Bundle\PatientBundle\Services\Location;

class MciExtension extends \Twig_Extension
{

    /**
     * Location
     */
    private $client;

    public function __construct(Location $client, $endpoint)
    {
        $this->client = $client;
        // $this->endpoint = $endpoint;
        $this->endpoint = "http://localhost/sample.json";
    }

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
            new \Twig_SimpleFilter('livingStatus', array($this, 'livingStatusFilter'))
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
        $data = $this->client->prepairFormData($this->client->getLocation());
        return isset($data[$number])?$data[$number]:'';
    }

    public function districtFilter($number)
    {
        $data = $this->client->prepairFormData($this->client->getLocation());
        return isset($data[$number])?$data[$number]:'';
    }

    public function upazilaFilter( $district_code='',$upazila_code='')
    {
        $data = $this->client->prepairFormData($this->client->getLocation($district_code.$upazila_code));
        return isset($data[$upazila_code])?$data[$upazila_code]:'';
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


    public function livingStatusFilter($number){
        $livingStatus = $this->getJsonData('livingStatus.json');
        return isset($livingStatus[$number])?$livingStatus[$number]:'';
    }


}
?>
