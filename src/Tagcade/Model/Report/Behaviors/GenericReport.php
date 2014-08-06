<?php

namespace Tagcade\Model\Report\Behaviors;

use DateTime;

trait GenericReport
{
    protected $id;
    protected $name;
    protected $date;

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
}