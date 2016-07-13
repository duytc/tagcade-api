<?php


namespace Tagcade\Repository\Report\UnifiedReport\Publisher;


use DateTime;
use Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Model\User\Role\PublisherInterface;

interface PublisherReportRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $batchSize = null);

    /**
     * @param PublisherReport $report
     * @return mixed
     */
    public function overrideSingleReport(PublisherReport $report);
}