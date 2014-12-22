<?php

namespace Mci\Bundle\PatientBundle\FormMapper;
use JMS\Serializer\Annotation\Type;
class Address
{
    /**
     * @Type("string")
     */
    private $address_line;
    /**
     * @Type("string")
     */
    private $holding_number;
    /**
     * @Type("string")
     */
    private $street;
    /**
     * @Type("string")
     */
    private $area_mouja;
    /**
     * @Type("string")
     */
    private $village;
    /**
     * @Type("string")
     */
    private $post_office;
    /**
     * @Type("string")
     */
    private $post_code;
    /**
     * @Type("string")
     */
    private $division_id;

    /**
     * @Type("string")
     */
    private $district_id;

    /**
     * @Type("string")
     */
    private $union_or_urban_ward_id;

    /**
     * @Type("string")
     */
    private $rural_ward_id;
    /**
     * @Type("string")
     */
    private $upazila_id;
    /**
     * @Type("string")
     */
    private $city_corporation_id;
    /**
     * @Type("string")
     */
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
     * @param mixed $upazila_id
     */
    public function setUpazilaId($upazila_id)
    {
        $this->upazila_id = $upazila_id;
    }

    /**
     * @return mixed
     */
    public function getUpazilaId()
    {
        return $this->upazila_id;
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
     * @param mixed $union_or_urban_ward_id
     */
    public function setUnionOrurbanwardId($union_or_urban_ward_id)
    {
        $this->union_or_urban_ward_id = $union_or_urban_ward_id;
    }

    /**
     * @return mixed
     */
    public function getUnionOrurbanwardId()
    {
        return $this->union_or_urban_ward_id;
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

    /**
     * @return mixed
     */
    public function getDistrictId()
    {
        return $this->district_id;
    }

    /**
     * @param mixed $district_id
     */
    public function setDistrictId($district_id)
    {
        $this->district_id = $district_id;
    }

    /**
     * @return mixed
     */
    public function getRuralWardId()
    {
        return $this->rural_ward_id;
    }

    /**
     * @param mixed $rural_ward_id
     */
    public function setRuralWardId($rural_ward_id)
    {
        $this->rural_ward_id = $rural_ward_id;
    }
}
