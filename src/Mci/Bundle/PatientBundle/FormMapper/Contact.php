<?php

namespace Mci\Bundle\PatientBundle\FormMapper;

class Contact
{
    private $number;
    private $country_code;
    private $area_code;
    private $extension;

    /**
     * @param mixed $area_code
     */
    public function setAreaCode($area_code)
    {
        $this->area_code = $area_code;
    }

    /**
     * @return mixed
     */
    public function getAreaCode()
    {
        return $this->area_code;
    }

    /**
     * @param mixed $country_code
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }




}
