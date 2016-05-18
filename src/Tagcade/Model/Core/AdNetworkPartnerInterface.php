<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\ModelInterface;

interface AdNetworkPartnerInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getNameCanonical();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return PersistentCollection
     */
    public function getPublisherPartners();

    /**
     * get Publisher Ids related to this ad network partner
     * @return array
     */
    public function getPublisherIds();

    /**
     * @param Collection|AdNetworkInterface[] $publisherPartners collection of AdNetworks
     * @return self
     */
    public function setPublisherPartners($publisherPartners);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return self
     */
    public function setUrl($url);

    /**
     * @return mixed
     */
    public function getTagIdRegex();

    /**
     * @param mixed $tagIdRegex
     * @return self
     */
    public function setTagIdRegex($tagIdRegex);

    /**
     * @return mixed
     */
    public function getTagSizeRegex();

    /**
     * @param mixed $tagSizeRegex
     * @return self
     */
    public function setTagSizeRegex($tagSizeRegex);
}