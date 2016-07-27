<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;

interface NetworkReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param $oneOrNull = false
     * @return mixed
     */
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $batchSize = null);

    /**
     * @param array $reports
     * @return mixed
     */
    public function createAdjustedCommonReports(array $reports);
}