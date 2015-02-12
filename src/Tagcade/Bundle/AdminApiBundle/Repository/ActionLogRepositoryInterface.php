<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;
use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;

interface ActionLogRepositoryInterface {

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $offset
     * @param int $limit
     * @param PublisherInterface $publisher
     * @param bool $loginLog
     * @return array
     */
    public function getLogsForDateRange(DateTime $startDate, DateTime $endDate, $offset = 0, $limit = 10, PublisherInterface $publisher = null, $loginLog = true);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param PublisherInterface $publisher
     * @param bool $loginLog
     * @return int
     */
    public function getTotalRows(DateTime $startDate, DateTime $endDate, PublisherInterface $publisher = null, $loginLog = true);

}