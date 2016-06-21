<?php


namespace Tagcade\Repository\Report\UnifiedReport\Publisher;


use DateTime;
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
     * @param $override = false
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $override = false, $batchSize = null);
}