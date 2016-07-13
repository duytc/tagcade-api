<?php


namespace Tagcade\Repository\Report\UnifiedReport\Publisher;


use DateTime;
use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherReportRepositoryInterface
{
    /**
     * @param SubPublisherInterface $subPublisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $batchSize = null);

    public function overrideSingleReport(SubPublisherReport $report);
}