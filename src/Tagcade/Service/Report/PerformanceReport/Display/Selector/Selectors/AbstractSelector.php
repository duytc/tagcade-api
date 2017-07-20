<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

abstract class AbstractSelector implements SelectorInterface
{
    /**
     * @inheritdoc
     */
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        $this->validateReportType($reportType);

        return $this->doGetReports($reportType, $startDate, $endDate, $queryParams);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param null|array $queryParams
     * @return array
     */
    abstract protected function doGetReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null);

    /**
     * validate report type
     * @param ReportTypeInterface $reportType
     */
    protected function validateReportType(ReportTypeInterface $reportType)
    {
        if (!$this->supportsReportType($reportType)) {
            throw new InvalidArgumentException(sprintf('Not support report type', $reportType->getReportType()));
        }
    }
}