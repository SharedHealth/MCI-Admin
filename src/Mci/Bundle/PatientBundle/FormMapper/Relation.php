<?php

namespace Mci\Bundle\PatientBundle\FormMapper;
use JMS\Serializer\Annotation\Type;
class Relation
{
    /**
     * @Type("string")
     */
    private $uid;
    /**
     * @Type("string")
     */
    private $nid;
    /**
     * @Type("string")
     */
    private $bin_brn;
    /**
     * @Type("string")
     */
    private $type;
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
    private $relational_status;

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
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
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
     * @return mixed
     */
    public function getRelationalStatus()
    {
        return $this->relational_status;
    }

    /**
     * @param mixed $relational_status
     */
    public function setRelationalStatus($relational_status)
    {
        $this->relational_status = $relational_status;
    }

}
