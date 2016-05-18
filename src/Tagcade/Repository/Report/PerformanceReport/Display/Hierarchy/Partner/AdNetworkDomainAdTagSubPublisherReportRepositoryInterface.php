<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface AdNetworkDomainAdTagSubPublisherReportRepositoryInterface
{
    /**
     * get report for an ad network and site domain
     * @param AdNetworkInterface $adNetwork
     * @param string $domain the domain of site
     * @param string $partnerTagId the partner tag's id
     * @param SubPublisherInterface $subPublisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull = false
     * @return mixed
     */
    public function getReportFor(AdNetworkInterface $adNetwork, $domain, $partnerTagId, SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param AdNetworkDomainAdTagSubPublisherReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkDomainAdTagSubPublisherReportInterface $report);
}