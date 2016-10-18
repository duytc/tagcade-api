<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Result;

use ArrayIterator;

class ExpandedReportCollection implements ReportResultInterface
{
    protected $expandedResult;
    protected $expandedReports;
    protected $originalResult;

    /**
     * @param ReportResultInterface $expandedResult
     * @param ReportResultInterface $originalResult
     */
    public function __construct(ReportResultInterface $expandedResult, ReportResultInterface $originalResult)
    {
        $this->expandedResult = $expandedResult;
        $this->expandedReports = $expandedResult->getReports();

        $this->originalResult = $originalResult;
    }

    public function getExpandedResult()
    {
        return $this->expandedResult;
    }

    public function getExpandedReports()
    {
        return $this->expandedReports;
    }

    public function getOriginalResult()
    {
        return $this->originalResult;
    }

    public function getReportType()
    {
        return $this->originalResult->getReportType();
    }

    public function getStartDate()
    {
        return $this->originalResult->getStartDate();
    }

    public function getEndDate()
    {
        return $this->originalResult->getEndDate();
    }

    public function getReports()
    {
        return $this->originalResult->getReports();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->expandedReports);
    }

    public function getName()
    {
        return $this->originalResult->getName();
    }
}