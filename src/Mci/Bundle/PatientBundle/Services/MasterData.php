<?php

namespace Mci\Bundle\PatientBundle\Services;

use Symfony\Component\HttpKernel\Config\FileLocator;

class MasterData
{
    /**
     * @var FileLocator
     */
    private $fileLocator;

    private $cache;

    public function __construct(FileLocator $fileLocator){

        $this->fileLocator = $fileLocator;
    }

    public function getNameByTypeAndCode($type, $code)
    {
        $values = $this->getAllByType($type);
        return isset($values[$code])?$values[$code]:'';
    }

    public function getAllByType($type)
    {
        if(!isset($this->cache[$type])) {
            $this->cache[$type] = $this->getJsonData($type);
        }

        return $this->cache[$type];
    }

    private function getJsonData($type)
    {
        $filePath =  $this->fileLocator->locate('@MciPatientBundle/Resources/master_data/' . $type .'.json');

        if(!$filePath && !file_exists($filePath)) {
            return array();
        }

        $values = json_decode(file_get_contents($filePath), true);

        if(json_last_error()) {
            return array();
        }

        return $values;
    }
}