<?php

namespace Tagcade\Service\Report\SourceReport\Selector;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ReportSelectorInterface {

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherByDayReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherBySiteReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPlatformByDayReport(DateTime $startDate, DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPlatformByPublisherReport(DateTime $startDate, DateTime $endDate);
}