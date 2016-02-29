<?php

namespace Tagcade\Model\Report\RtbReport\ReportType\Hierarchy;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\RtbReport\Hierarchy\AdSlotReportInterface;
use Tagcade\Model\Report\RtbReport\Hierarchy\SiteReportInterface;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\AbstractCalculatedReportType;

class Site extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'site';

    /**
     * @var SiteInterface
     */
    private $site;

    public function __construct(SiteInterface $site)
    {
        $this->site = $site;
    }

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
        return $this->site->getId();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }
}