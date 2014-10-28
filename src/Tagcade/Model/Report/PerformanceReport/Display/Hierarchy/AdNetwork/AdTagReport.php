<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Core\AdTagInterface;

class AdTagReport extends AbstractReport implements AdTagReportInterface
{
    const REPORT_TYPE = 'adNetwork.adTag';

    use SuperReportTrait;

    /**
     * @var AdTagInterface
     */
    protected $adTag;

    /**
     * @return AdTagInterface|null
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @return int|null
     */
    public function getAdTagId()
    {
        if ($this->adTag instanceof AdTagInterface) {
            return $this->adTag->getId();
        }

        return null;
    }

    /**
     * @param AdTagInterface $adTag
     * @return $this
     */
    public function setAdTag(AdTagInterface $adTag)
    {
        $this->adTag = $adTag;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function setFillRate()
    {
        // note that we use slot opportunities to calculate fill rate in this Reports
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());

        return $this;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    protected function setDefaultName()
    {
        if ($this->adTag instanceof AdTagInterface) {
            $this->setName($this->adTag->getName());
        }
    }
}