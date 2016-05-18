<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ReportTypeInterface
{
    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @return int
     */
    public function getPublisherId();

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork();

    /**
     * @return int
     */
    public function getAdNetworkId();

    /**
     * @return string|null
     */
    public function getReportType();

    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    public function matchesReport(ReportInterface $report);
}