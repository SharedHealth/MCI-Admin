<?php

namespace Mci\Bundle\PatientBundle\FormMapper;
use JMS\Serializer\Annotation\Type;

class Status
{
    /**
     * @Type("string")
     */
    private $type;


    /**
     * @Type("string")
     */
    private $date_of_death;

    /**
     * @return mixed
     */
    public function getDateOfDeath()
    {
        return $this->date_of_death;
    }

    /**
     * @param mixed $date_of_death
     */
    public function setDateOfDeath($date_of_death)
    {
        $this->date_of_death = $date_of_death;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}