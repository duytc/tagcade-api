<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkDomainReportRepositoryInterface
{
    /**
     * get report for an ad network and site domain
     * @param AdNetworkInterface $adNetwork
     * @param string $domain the domain of site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param $oneOrNull = false
     * @return mixed
     */
    public function getReportFor(AdNetworkInterface $adNetwork, $domain, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * get report for site domain for all partners
     *
     * @param string $domain
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getSiteReportForAllPartners($domain, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSiteReportForAllPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param AdNetworkDomainReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkDomainReportInterface $report);
}