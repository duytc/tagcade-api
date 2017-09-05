<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\CalculateNetworkOpportunityFillRateTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class SiteReport extends AbstractCalculatedReport implements SiteReportInterface, ImpressionBreakdownReportDataInterface
{
    use SuperReportTrait;
    use CalculateNetworkOpportunityFillRateTrait;

    /** @var SiteInterface */
    protected $site;

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return int|null
     */
    public function getSiteId()
    {
        if ($this->site instanceof SiteInterface) {
            return $this->site->getId();
        }

        return null;
    }

    /**
     * @param SiteInterface|null $site
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkReportInterface;
    }

    /**
     * @inheritdoc
     */
    protected function doCalculateFields()
    {
        parent::doCalculateFields();

        // difference calculate at network/site level
        $this->setNetworkOpportunityFillRate($this->calculateNetworkOpportunityFillRate($this->getAdOpportunities(), $this->getTotalOpportunities()));
    }

    protected function setDefaultName()
    {
        if ($this->site instanceof SiteInterface) {
            $this->setName($this->site->getName());
        }
    }
}