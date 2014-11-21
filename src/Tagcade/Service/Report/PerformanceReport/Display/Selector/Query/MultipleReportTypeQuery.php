<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Query;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class MultipleReportTypeQuery extends AbstractReportQuery implements ReportQueryInterface
{

    /**
     * @var ReportTypeInterface[]
     */
    private $reportTypes;

    function __construct(array $reportTypes, DateTime $startDate, DateTime $endDate, $expanded = false, $grouped = false)
    {
        foreach($reportTypes as $reportType) {
            if (!$reportType instanceof ReportTypeInterface) {
                throw new InvalidArgumentException('MultipleReportQuery expected ReportTypeInterface only');
            }
        }

        parent::__construct($startDate, $endDate, $expanded, $grouped);

        $this->reportTypes = $reportTypes;
    }

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return $this->reportTypes;
    }


} 