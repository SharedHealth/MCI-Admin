<?php

namespace Mci\Bundle\PatientBundle\FormMapper;

class Address
{
    private $address_line;
    private $holding_number;
    private $street;
    private $area_mouja;
    private $village;
    private $post_office;
    private $post_code;
    private $division_id;
    private $union_id;
    private $upazilla_id;
    private $city_corporation_id;
    private $country_code;

    /**
     * @param mixed $address_line
     */
    public function setAddressLine($address_line)
    {
        $this->address_line = $address_line;
    }

    /**
     * @return mixed
     */
    public function getAddressLine()
    {
        return $this->address_line;
    }

    /**
     * @param mixed $village
     */
    public function setVillage($village)
    {
        $this->village = $village;
    }

    /**
     * @return mixed
     */
    public function getVillage()
    {
        return $this->village;
    }

    /**
     * @param mixed $upazilla_id
     */
    public function setUpazillaId($upazilla_id)
    {
        $this->upazilla_id = $upazilla_id;
    }

    /**
     * @return mixed
     */
    public function getUpazillaId()
    {
        return $this->upazilla_id;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $union_id
     */
    public function setUnionId($union_id)
    {
        $this->union_id = $union_id;
    }

    /**
     * @return mixed
     */
    public function getUnionId()
    {
        return $this->union_id;
    }

    /**
     * @param mixed $post_office
     */
    public function setPostOffice($post_office)
    {
        $this->post_office = $post_office;
    }

    /**
     * @return mixed
     */
    public function getPostOffice()
    {
        return $this->post_office;
    }

    /**
     * @param mixed $holding_number
     */
    public function setHoldingNumber($holding_number)
    {
        $this->holding_number = $holding_number;
    }

    /**
     * @return mixed
     */
    public function getHoldingNumber()
    {
        return $this->holding_number;
    }

    /**
     * @param mixed $post_code
     */
    public function setPostCode($post_code)
    {
        $this->post_code = $post_code;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * @param mixed $division_id
     */
    public function setDivisionId($division_id)
    {
        $this->division_id = $division_id;
    }

    /**
     * @return mixed
     */
    public function getDivisionId()
    {
        return $this->division_id;
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
     * @param mixed $city_corporation_id
     */
    public function setCityCorporationId($city_corporation_id)
    {
        $this->city_corporation_id = $city_corporation_id;
    }

    /**
     * @return mixed
     */
    public function getCityCorporationId()
    {
        return $this->city_corporation_id;
    }

    /**
     * @param mixed $area_mouja
     */
    public function setAreaMouja($area_mouja)
    {
        $this->area_mouja = $area_mouja;
    }

    /**
     * @return mixed
     */
    public function getAreaMouja()
    {
        return $this->area_mouja;
    }
}
