<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

interface NetworkSiteReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param $domain
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor($adNetwork, $domain, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $batchSize = null);

    /**
     * @param Params $params
     * @return array
     */
    public function getAllDistinctDomains(Params $params);

    /**
     * @param AdNetworkInterface $partner
     * @param Params $params
     * @return array
     */
    public function getAllDistinctDomainsForPartner(AdNetworkInterface $partner, Params $params);
}