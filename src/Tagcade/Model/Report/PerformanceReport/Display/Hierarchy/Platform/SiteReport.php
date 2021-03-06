<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Core\SiteInterface;

class SiteReport extends AbstractCalculatedReport implements SiteReportInterface
{
    use SuperReportTrait;
    use CalculateAdOpportunitiesTrait;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @inheritdoc
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
        return $report instanceof AdSlotReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    /**
     * @inheritdoc
     */
    protected function doCalculateFields()
    {
        parent::doCalculateFields();

        // difference calculate at site level
        $this->setOpportunityFillRate($this->calculateOpportunityFillRate($this->getAdOpportunities(), $this->getSlotOpportunities()));
    }

    protected function setDefaultName()
    {
        if ($this->site instanceof SiteInterface) {
            $this->setName($this->site->getName());
        }
    }
}