<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators;


use Tagcade\Model\Report\RtbReport\ReportInterface;

interface RtbSnapshotCreatorInterface extends RtbCreatorInterface
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