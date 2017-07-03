<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class Site extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.site';

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

    public function getSiteName()
    {
        return $this->site->getName();
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