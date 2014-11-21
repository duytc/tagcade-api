<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Exception\InvalidArgumentException;
use DateTime;

final class Collection
{
    /**
     * @var ReportTypeInterface
     */
    private $reportType;

    /**
     * @var string
     */
    private $reportName;
    /**
     * @var DateTime
     */
    private $startDate;
    /**
     * @var DateTime
     */
    private $endDate;
    /**
     * @var ReportInterface[]
     */
    private $reports;

    /**
     * @param ReportTypeInterface $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportInterface[] $reports
     */
    public function __construct(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, array $reports)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        foreach($reports as $report) {
            if (!$reportType->isValidReport($report)) {
                throw new InvalidArgumentException('You tried to add reports to a collection that did not match the supplied report type');
            }

            if (null === $this->reportName) {
                $this->reportName = $report->getName();
            }
        }

        $this->reports = $reports;
    }

    /**
     * @return ReportTypeInterface
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return string|null
     */
    public function getReportName()
    {
        return $this->reportName;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return array|\Tagcade\Model\Report\PerformanceReport\Display\ReportInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }
}