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
}