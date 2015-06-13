<?php

namespace Tagcade\Model\Core;

use Tagcade\Bundle\AdminApiBundle\Entity\SourceReportEmailConfig;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Site implements SiteInterface
{
    protected $id;

    /**
     * @var UserEntityInterface
     */
    protected $publisher;
    protected $name;
    protected $domain;
    protected $adSlots;
    protected $enableSourceReport;
    /**
     * @var SourceReportSiteConfigInterface[]
     */
    protected $sourceReportSiteConfigs;

    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $dynamicAdSlots;

    /**
     * @var NativeAdSlotInterface[]
     */
    protected $nativeAdSlots;
    /**
     * @param string $name
     * @param string $domain
     */
    public function __construct($name, $domain)
    {
        $this->name = $name;
        $this->domain = $domain;
        $this->adSlots = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        if (!$this->publisher) {
            return null;
        }

        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher) {
        $this->publisher = $publisher->getUser();
        return $this;
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
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @inheritdoc
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDisplayAdSlots()
    {
        $this->adSlots;
    }
    /**
     * @inheritdoc
     */
    public function getReportableAdSlots()
    {
        if (null === $this->nativeAdSlots) {
            $this->nativeAdSlots = new ArrayCollection();
        }

        if (null === $this->adSlots ) {
            $this->adSlots = new ArrayCollection();
        }

        return array_merge($this->adSlots->toArray(), $this->nativeAdSlots->toArray());
    }

    /**
     * @inheritdoc
     */
    public function getNativeAdSlots()
    {
        return $this->nativeAdSlots;
    }

    /**
     * @return DynamicAdSlotInterface[]
     */
    public function getDynamicAdSlots()
    {
        return $this->dynamicAdSlots;
    }

    public function getAllAdSlots()
    {
        if (null === $this->adSlots ) {
            $this->adSlots = new ArrayCollection();
        }

        if (null === $this->nativeAdSlots) {
            $this->nativeAdSlots = new ArrayCollection();
        }

        if (null === $this->dynamicAdSlots){
            $this->dynamicAdSlots = new ArrayCollection();
        }

        return array_merge($this->adSlots->toArray(), $this->nativeAdSlots->toArray(), $this->dynamicAdSlots->toArray());
    }

    public function __toString()
    {
        return $this->name;
    }



    /**
     * @inheritdoc
     */
    public function getEnableSourceReport()
    {
        return $this->enableSourceReport;
    }

    /**
     * @inheritdoc
     */
    public function setEnableSourceReport($enableSourceReport)
    {
        $this->enableSourceReport = $enableSourceReport;
    }

    public function getSourceReportSiteConfigs()
    {
        return $this->sourceReportSiteConfigs;
    }

}
