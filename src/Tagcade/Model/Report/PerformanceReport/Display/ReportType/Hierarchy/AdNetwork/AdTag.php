<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class AdTag extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'adNetwork.adTag';

    /**
     * @var AdTagInterface
     */
    private $adTag;

    public function __construct(AdTagInterface $adTag)
    {
        $this->adTag = $adTag;
    }

    /**
     * @return AdTagInterface
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
        return $this->adTag->getId();
    }

    /**
     * @inheritdoc
     */
    public function isExpandable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isValidReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }
}