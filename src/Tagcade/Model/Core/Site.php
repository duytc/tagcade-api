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
        return array_filter($this->adSlots->toArray(), function (BaseAdSlotInterface $adSlot) { return $adSlot instanceof ReportableAdSlotInterface; });
    }

    public function getAllAdSlots()
    {
        return $this->adSlots;
    }

    public function __toString()
    {
        return $this->id . $this->getName();
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
