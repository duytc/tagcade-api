<?php

namespace Tagcade\Bundle\AdminApiBundle\Entity;


use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\Core\SiteInterface;

class SourceReportSiteConfig implements SourceReportSiteConfigInterface
{
    protected $id;
    /**
     * @var SourceReportEmailConfigInterface
     */
    protected $sourceReportEmailConfig;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param SiteInterface $site
     * @return $this
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return SourceReportEmailConfigInterface
     */
    public function getSourceReportEmailConfig()
    {
        return $this->sourceReportEmailConfig;
    }

    /**
     * @param SourceReportEmailConfigInterface $sourceReportEmailConfig
     */
    public function setSourceReportEmailConfig($sourceReportEmailConfig)
    {
        $this->sourceReportEmailConfig = $sourceReportEmailConfig;
    }




} 