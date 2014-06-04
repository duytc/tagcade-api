<?php

namespace Tagcade\Model\Report\SourceReport;

class TrackingTerm
{
    protected $id;

    /**
     * @var string
     */
    protected $term;

    /**
     * @param string $term
     */
    public function __construct($term)
    {
        $this->term = $term;
    }

    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setTerm($name)
    {
        $this->term = $name;

        return $this;
    }
}