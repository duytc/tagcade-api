<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;

interface NetworkAdTagReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param $partnerTagId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor($adNetwork, $partnerTagId, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $batchSize = null
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $batchSize = null);
}