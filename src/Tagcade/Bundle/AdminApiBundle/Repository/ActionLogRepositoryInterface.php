<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;
use DateTime;

interface ActionLogRepositoryInterface {

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getLogsForDateRange(DateTime $startDate, DateTime $endDate, $offset, $limit );

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return int
     */
    public function getTotalRecords(DateTime $startDate, DateTime $endDate);

}