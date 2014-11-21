<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Query;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class SingleReportTypeQuery extends AbstractReportQuery implements ReportQueryInterface
{
    /**
     * @var ReportTypeInterface
     */
    protected $reportType;

    function __construct(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $expanded = false, $grouped = false)
    {
        parent::__construct($startDate, $endDate, $expanded, $grouped);

        $this->reportType = $reportType;
    }

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return $this->reportType;
    }
}