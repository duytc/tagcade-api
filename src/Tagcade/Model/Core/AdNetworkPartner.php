<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class AdNetworkPartner implements AdNetworkPartnerInterface
{
    protected $id;
    protected $name;
    protected $nameCanonical;
    /**
     * @var array
     */
    protected $reportTypes;

    /**
     * @var PersistentCollection
     */
    protected $publisherPartners;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getId();
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getNameCanonical()
    {
        return $this->nameCanonical;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->nameCanonical = strtolower($name);
    }

    /**
     * @return PersistentCollection
     */
    public function getPublisherPartners()
    {
        if (null === $this->publisherPartners) {
            $this->publisherPartners = new ArrayCollection();
        }

        return $this->publisherPartners;
    }

    /**
     * @param PublisherPartner[] $publisherPartners
     */
    public function setPublisherPartners($publisherPartners)
    {
        $this->publisherPartners = $publisherPartners;
    }

    /**
     * @return array
     */
    public function getReportTypes()
    {
        return $this->reportTypes;
    }

    /**
     * @param array $reportTypes
     */
    public function setReportTypes($reportTypes)
    {
        $this->reportTypes = $reportTypes;
    }

}