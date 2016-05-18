<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class AdNetworkPartner implements AdNetworkPartnerInterface
{
    protected $id;
    protected $name;
    protected $nameCanonical;
    protected $url;
    /**
     * @var array
     */
    protected $reportTypes;
    /** string regex for parsing tagId from tag html */
    protected $tagIdRegex;
    /** string regex for parsing tagSize from tag html */
    protected $tagSizeRegex;

    /**
     * important: mapping to AdNetwork which related to Publisher!!!
     * @var PersistentCollection collection of AdNetworks
     */
    protected $publisherPartners;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getNameCanonical()
    {
        return $this->nameCanonical;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->nameCanonical = $this->normalizeName($name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherPartners()
    {
        if (null === $this->publisherPartners) {
            $this->publisherPartners = new ArrayCollection();
        }

        return $this->publisherPartners;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherIds()
    {
        /** @var Collection|AdNetworkInterface[] $publisherPartners */
        $publisherPartners = $this->getPublisherPartners();

        $publisherPartners = $publisherPartners instanceof Collection ? $publisherPartners->toArray() : $publisherPartners;

        return array_map(function($adNetwork) {
            /** @var AdNetworkInterface $adNetwork */
            return $adNetwork->getPublisherId();
        }, $publisherPartners);
    }

    /**
     * @inheritdoc
     */
    public function setPublisherPartners($publisherPartners)
    {
        $this->publisherPartners = $publisherPartners;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReportTypes()
    {
        return $this->reportTypes;
    }

    /**
     * @inheritdoc
     */
    public function setReportTypes($reportTypes)
    {
        $this->reportTypes = $reportTypes;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTagIdRegex()
    {
        return $this->tagIdRegex;
    }

    /**
     * @inheritdoc
     */
    public function setTagIdRegex($tagIdRegex)
    {
        $this->tagIdRegex = $tagIdRegex;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTagSizeRegex()
    {
        return $this->tagSizeRegex;
    }

    /**
     * @inheritdoc
     */
    public function setTagSizeRegex($tagSizeRegex)
    {
        $this->tagSizeRegex = $tagSizeRegex;

        return $this;
    }

    private function normalizeName($name)
    {
        $name = strtolower($name);

        $string = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}