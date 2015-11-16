<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface SnapshotCreatorInterface extends CreatorInterface
{
    /**
     * Parse report data and set to model class
     *
     * @param ReportInterface $report
     * @param array $redisReportData
     *
     * @return void
     */
    public function parseRawReportData(ReportInterface $report, array $redisReportData);
}