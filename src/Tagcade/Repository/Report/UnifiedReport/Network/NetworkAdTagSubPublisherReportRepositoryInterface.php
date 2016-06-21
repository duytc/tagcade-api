<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface NetworkAdTagSubPublisherReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param string $partnerTagId
     * @param SubPublisherInterface $subPublisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(AdNetworkInterface $adNetwork, $partnerTagId, SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param string $partnerTagId
     * @param SubPublisherInterface $subPublisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportForAllAdNetwork($partnerTagId, SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $override = false
     * @param $batchSize = null
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $override = false, $batchSize = null);
}