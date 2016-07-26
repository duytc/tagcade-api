<?php


namespace Tagcade\Repository\Report\UnifiedReport\Publisher;


use DateTime;
use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherNetworkReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherNetworkReportRepositoryInterface
{
    /**
     * @param SubPublisherInterface $subPublisher
     * @param AdNetworkInterface $adNetwork
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param array $reports
     * @param $batchSize = null
     *
     * @return mixed
     */
    public function saveMultipleReport(array $reports, $batchSize = null);

    public function createAdjustedCommonReports(array $reports);
}