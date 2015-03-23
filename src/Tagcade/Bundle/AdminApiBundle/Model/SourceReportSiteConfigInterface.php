<?php

namespace Tagcade\Bundle\AdminApiBundle\Model;


use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;

interface SourceReportSiteConfigInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return SourceReportEmailConfigInterface
     */
    public function getSourceReportEmailConfig();

    /**
     * @param SourceReportEmailConfigInterface $sourceReportEmailConfig
     */
    public function setSourceReportEmailConfig($sourceReportEmailConfig);

    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @param SiteInterface $site
     * @return $this
     */
    public function setSite(SiteInterface $site);
} 