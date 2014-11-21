<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Exception\InvalidArgumentException;
use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

final class ReportCollection implements ReportResultInterface
{
    /**
     * @var ReportTypeInterface
     */
    private $reportType;

    /**
     * @var string
     */
    private $name;
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

    private $isExpanded = false;

    /**
     * @param ReportTypeInterface $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportInterface[] $reports
     * @param bool $expand
     */
    public function __construct(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, array $reports, $expand = false)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        foreach($reports as $report) {
            if (!$reportType->isValidReport($report)) {
                throw new InvalidArgumentException('You tried to add reports to a collection that did not match the supplied report type');
            }

            if (null === $this->name) {
                $this->name = $report->getName();
            }
        }

        if ($expand && $reportType->isExpandable()) {
            $reports = array_map(function(SuperReportInterface $report) {
                return $report->getSubReports();
            }, $reports);

            $this->isExpanded = true;
        }

        $this->reports = $reports;
    }

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @inheritdoc
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return bool
     */
    public function isExpanded()
    {
        return $this->isExpanded;
    }
}