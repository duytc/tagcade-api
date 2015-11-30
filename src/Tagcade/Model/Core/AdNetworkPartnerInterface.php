<?php

namespace Tagcade\Model\Core;


use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\ModelInterface;

interface AdNetworkPartnerInterface extends ModelInterface
{
    public function getName();

    /**
     * @return mixed
     */
    public function getNameCanonical();
    /**
     * @param mixed $name
     */
    public function setName($name);

    /**
     * @return PersistentCollection
     */
    public function getPublisherPartners();

    /**
     * @param PublisherPartner[] $publisherPartners
     */
    public function setPublisherPartners($publisherPartners);

}