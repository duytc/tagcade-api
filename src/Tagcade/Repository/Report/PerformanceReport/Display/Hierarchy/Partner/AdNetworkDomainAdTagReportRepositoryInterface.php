<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkDomainAdTagReportRepositoryInterface
{
    /**
     * get report for an ad network and site domain
     * @param AdNetworkInterface $adNetwork
     * @param string $domain the domain of site
     * @param string $partnerTagId the partner tag's id
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param $oneOrNull = false
     * @return mixed
     */
    public function getReportFor(AdNetworkInterface $adNetwork, $domain, $partnerTagId, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getAllReportsFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param AdNetworkDomainAdTagReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkDomainAdTagReportInterface $report);
}