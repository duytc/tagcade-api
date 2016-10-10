<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

class Site extends AbstractReportType implements CalculatedReportTypeInterface
{
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