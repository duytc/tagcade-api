<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Service\Core\Site\SiteServiceInterface;

interface NetworkDomainAdTagReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param string $partnerTagId
     * @param string $domain
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor($adNetwork, $domain, $partnerTagId, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $override = false
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $override = false);

    public function setSiteService(SiteServiceInterface $siteService);

    public function setSubPublisherManager(SubPublisherManagerInterface $subPublisherManager);

    /**
     * Check if the given composition key existed or not
     * @param $adNetwork
     * @param $domain
     * @param $partnerTagId
     * @param DateTime $date
     * @return mixed
     */
    public function isRecordExisted(AdNetworkInterface $adNetwork, $domain, $partnerTagId, DateTime $date);
}