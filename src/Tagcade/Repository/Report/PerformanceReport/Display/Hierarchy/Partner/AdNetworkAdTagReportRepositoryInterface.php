<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkAdTagReportRepositoryInterface
{
    /**
     * get report for a partner tag id and a/all partner(s)
     * @param string $partnerTagId
     * @param AdNetworkInterface|null $adNetwork if null, this will return reports for all partner by $partnerTagId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(AdNetworkInterface $adNetwork = null, $partnerTagId, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getAllReportsFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param AdNetworkAdTagReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkAdTagReportInterface $report);
}