<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

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
     * @param $override = false
     * @param $batchSize = null
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $override = false, $batchSize = null);

    /**
     * @param Params $params
     * @return array
     */
    public function getAllDistinctAdTags(Params $params);

    /**
     * @param AdNetworkInterface $partner
     * @param Params $params
     * @return array
     */
    public function getAllDistinctAdTagsForPartner(AdNetworkInterface $partner, Params $params);
}