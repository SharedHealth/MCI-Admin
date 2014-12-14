<?php

namespace Mci\Bundle\PatientBundle\FormMapper;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;

class Patient
{
    /**
     * @Type("string")
     */
    private $nid;
    /**
     * @Type("string")
     */
    private $uid;
    /**
     * @Type("string")
     */
    private $is_alive;
    /**
     * @Type("string")
     */
    private $bin_brn;
    /**
     * @Type("string")
     */
    private $sur_name;
    /**
     * @Type("string")
     */
    private $given_name;
    /**
     * @Type("string")
     */

    private $name_bangla;
    /**
     * @Type("string")
     */

    private $date_of_birth;
    /**
     * @Type("string")
     */
    private $place_of_birth;
    /**
     * @Type("string")
     */

    private $nationality;
    /**
     * @Type("string")
     */
    private $primary_contact;
    /**
     * @Type("string")
     */

    private $gender;
    /**
     * @Type("string")
     */
    private $ethnicity;
    /**
     * @Type("string")
     */
    private $religion;
    /**
     * @Type("string")
     */
    private $blood_group;
    /**
     * @Type("string")
     */
    private $occupation;
    /**
     * @Type("string")
     */
    private $edu_level;
    /**
     * @Type("string")
     */
    private $disability;
    /**
     * @Type("string")
     */
    private $marital_status;
    /**
     * @Type("Mci\Bundle\PatientBundle\FormMapper\Address")
     */
    private $present_address;
    /**
     * @Type("Mci\Bundle\PatientBundle\FormMapper\Address")
     */
    private $permanent_address;
    /**
     * @Type("Mci\Bundle\PatientBundle\FormMapper\Contact")
     */
    private $phone_number;
    /**
     * @Type("Mci\Bundle\PatientBundle\FormMapper\Contact")
     */
    private $primary_contact_number;
    /**
     * @Type("ArrayCollection<Mci\Bundle\PatientBundle\FormMapper\Relation>")
     */
    protected  $relations;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
    }


    public function addRelations(Relation $relation)
    {
        $this->relations->add($relation);
        return $this;
    }


    public function removeRelations(Relation $relation){
        $this->relations->removeElement($relation);
    }

    /**
     * @param mixed $bin_brn
     */
    public function setBinBrn($bin_brn)
    {
        $this->bin_brn = $bin_brn;
    }

    /**
     * @return mixed
     */
    public function getBinBrn()
    {
        return $this->bin_brn;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $sur_name
     */
    public function setSurName($sur_name)
    {
        $this->sur_name = $sur_name;
    }

    /**
     * @return mixed
     */
    public function getSurName()
    {
        return $this->sur_name;
    }

    /**
     * @param mixed $relations
     */
    public function setRelations(Relation $relations)
    {
        $this->relations = $relations;
    }

    /**
     * @return mixed
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param mixed $primary_contact_number
     */
    public function setPrimaryContactNumber($primary_contact_number)
    {
        $this->primary_contact_number = $primary_contact_number;
    }

    /**
     * @return mixed
     */
    public function getPrimaryContactNumber()
    {
        return $this->primary_contact_number;
    }

    /**
     * @param mixed $present_address
     */
    public function setPresentAddress($present_address)
    {
        $this->present_address = $present_address;
    }

    /**
     * @return mixed
     */
    public function getPresentAddress()
    {
        return $this->present_address;
    }

    /**
     * @param mixed $blood_group
     */
    public function setBloodGroup($blood_group)
    {
        $this->blood_group = $blood_group;
    }

    /**
     * @return mixed
     */
    public function getBloodGroup()
    {
        return $this->blood_group;
    }

    /**
     * @param mixed $disability
     */
    public function setDisability($disability)
    {
        $this->disability = $disability;
    }

    /**
     * @return mixed
     */
    public function getDisability()
    {
        return $this->disability;
    }

    /**
     * @param mixed $edu_level
     */
    public function setEduLevel($edu_level)
    {
        $this->edu_level = $edu_level;
    }

    /**
     * @return mixed
     */
    public function getEduLevel()
    {
        return $this->edu_level;
    }

    /**
     * @param mixed $ethnicity
     */
    public function setEthnicity($ethnicity)
    {
        $this->ethnicity = $ethnicity;
    }

    /**
     * @return mixed
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $marital_status
     */
    public function setMaritalStatus($marital_status)
    {
        $this->marital_status = $marital_status;
    }

    /**
     * @return mixed
     */
    public function getMaritalStatus()
    {
        return $this->marital_status;
    }

    /**
     * @param mixed $given_name
     */
    public function setGivenName($given_name)
    {
        $this->given_name = $given_name;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->given_name;
    }

    /**
     * @param mixed $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param mixed $name_bangla
     */
    public function setNameBangla($name_bangla)
    {
        $this->name_bangla = $name_bangla;
    }

    /**
     * @return mixed
     */
    public function getNameBangla()
    {
        return $this->name_bangla;
    }

    /**
     * @param mixed $nid
     */
    public function setNid($nid)
    {
        $this->nid = $nid;
    }

    /**
     * @return mixed
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * @param mixed $occupation
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    }

    /**
     * @return mixed
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param mixed $permanent_address
     */
    public function setPermanentAddress($permanent_address)
    {
        $this->permanent_address = $permanent_address;
    }

    /**
     * @return mixed
     */
    public function getPermanentAddress()
    {
        return $this->permanent_address;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param mixed $place_of_birth
     */
    public function setPlaceOfBirth($place_of_birth)
    {
        $this->place_of_birth = $place_of_birth;
    }

    /**
     * @return mixed
     */
    public function getPlaceOfBirth()
    {
        return $this->place_of_birth;
    }

    /**
     * @param mixed $primary_contact
     */
    public function setPrimaryContact($primary_contact)
    {
        $this->primary_contact = $primary_contact;
    }

    /**
     * @return mixed
     */
    public function getPrimaryContact()
    {
        return $this->primary_contact;
    }

    /**
     * @param mixed $religion
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;
    }

    /**
     * @return mixed
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * @param mixed $date_of_birth
     */
    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;
    }

    /**
     * @return mixed
     */
    public function getIsAlive()
    {
        return $this->is_alive;
    }

    /**
     * @param mixed $is_alive
     */
    public function setIsAlive($is_alive)
    {
        $this->is_alive = $is_alive;
    }


}
