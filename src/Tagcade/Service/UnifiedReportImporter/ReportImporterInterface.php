<?php


namespace Tagcade\Service\UnifiedReportImporter;


use Tagcade\Model\Core\AdNetworkInterface;

interface ReportImporterInterface
{
    /**
     * Do import reports and return date range of imported reports.
     *
     * @param AdNetworkInterface $adNetwork
     * @param array $reports
     * @param $override
     * @return array('startDate'=> $startDate, 'endDate' => $endDate)|false
     */
    public function importReports(AdNetworkInterface $adNetwork, array $reports, $override);
}