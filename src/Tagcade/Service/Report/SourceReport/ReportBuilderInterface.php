<?php


namespace Tagcade\Service\Report\SourceReport;


use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ReportBuilderInterface
{
    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSiteReport(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param array $publishers
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherReports(array $publishers, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherByDayReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    public function getPublisherBySiteReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    public function getPlatformByDayReport(DateTime $startDate, DateTime $endDate);

    public function getPlatformByPublisherReport(DateTime $startDate, DateTime $endDate);
}