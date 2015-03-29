<?php
namespace Mci\Bundle\PatientBundle\Twig;


use Mci\Bundle\PatientBundle\Services\Location;
use Mci\Bundle\PatientBundle\Services\MasterData;

class MciExtension extends \Twig_Extension
{
    /**
     * Location
     */
    private $client;
    /**
     * @var MasterData
     */
    private $masterData;

    public function __construct(Location $client, MasterData $masterData)
    {
        $this->client = $client;
        $this->masterData = $masterData;
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
            new \Twig_SimpleFilter('location', array($this, 'locationFilter')),
            new \Twig_SimpleFilter('countrycode', array($this, 'countryCodeFilter')),
            new \Twig_SimpleFilter('maritalStatus', array($this, 'maritalStatusFilter')),
            new \Twig_SimpleFilter('relation', array($this, 'relationFilter')),
            new \Twig_SimpleFilter('livingStatus', array($this, 'livingStatusFilter')),
            'camelize' => new \Twig_Filter_Method($this, 'camelizeFilter')
        );
    }

    public function genderFilter($number)
    {
        return $this->masterData->getNameByTypeAndCode('gender', $number);
    }

    public function occupationFilter($number)
    {
        return $this->masterData->getNameByTypeAndCode('occupation', $number);
    }

    public function eduLevelFilter($number)
    {
        return $this->masterData->getNameByTypeAndCode('education_level', $number);
    }

    public function bloodGroupFilter($number)
    {
        return $this->masterData->getNameByTypeAndCode('blood_group', $number);
    }

    public function religionFilter($number)
    {
        return $this->masterData->getNameByTypeAndCode('religion', $number);
    }

    public function disabilityFilter($number)
    {
        return $this->masterData->getNameByTypeAndCode('disability', $number);
    }

    public function divisionFilter($number)
    {
        $data = $this->client->prepairFormData($this->client->getLocation());
        return isset($data[$number])?$data[$number]:'';
    }

    public function locationFilter($number,$locationCode)
    {
        $cache = array();
        if(empty($cache[$locationCode])){
            $cache[$locationCode] = $this->client->getLocation($locationCode);
        }
        $data = $this->client->prepairFormData($cache[$locationCode]);
        return isset($data[$number])?$data[$number]:'';
    }

    public function countryCodeFilter($number)
        {
            return $this->masterData->getNameByTypeAndCode('country_code', $number);
        }
    public function maritalStatusFilter($number)
        {
            return $this->masterData->getNameByTypeAndCode('marital_status', $number);
        }
    public function relationFilter($number)
        {
            return $this->masterData->getNameByTypeAndCode('religion', $number);
        }

    public function getName()
    {
        return 'mci_extension';
    }


    public function livingStatusFilter($number){
        return $this->masterData->getNameByTypeAndCode('status', $number);
    }

    public function camelizeFilter($value)
    {
        if(!is_string($value)) {
            return $value;
        }

        $chunks    = explode('_', $value);
        if(isset($chunks['1']) && ($chunks['1'] == 'id' || $chunks['1'] == 'code')){
            return ucfirst($chunks['0']);
        }
        elseif(isset($chunks['2']) &&  $chunks['2'] == 'id'){
            return ucfirst($chunks['0']).ucfirst($chunks['1']);
        }elseif($value == 'union_or_urban_ward_id'){
            return "Union Or Urban Ward";
        }

        $ucfirsted = array_map(function($s) { return ucfirst($s); }, $chunks);

        return implode(' ', $ucfirsted);
    }

}
