<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface NetworkDomainAdTagSubPublisherReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param string $partnerTagId
     * @param SubPublisherInterface $subPublisher
     * @param string $domain
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor($adNetwork, $domain, $partnerTagId, SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $override = false
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $override = false, $batchSize = null);

    /**
     * @param SubPublisherInterface $subPublisher
     * @param AdNetworkInterface $adNetwork
     * @param $domain
     * @param $partnerTagId
     * @param DateTime $date
     * @return mixed
     */
    public function isRecordExisted(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, $domain, $partnerTagId, DateTime $date);
}