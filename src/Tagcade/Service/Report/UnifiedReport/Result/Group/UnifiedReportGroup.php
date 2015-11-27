<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group;

class UnifiedReportGroup
{
    protected $reportType;
    protected $reports;
    protected $name;
    protected $startDate;
    protected $endDate;
    private $paidImps;
    private $totalImps;

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, array $reports, $name, $paidImps, $totalImps)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
        $this->name = $name;

        $this->paidImps = $paidImps;
        $this->totalImps = $totalImps;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return array
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getPaidImps()
    {
        return $this->paidImps;
    }

    /**
     * @return mixed
     */
    public function getTotalImps()
    {
        return $this->totalImps;
    }


} 