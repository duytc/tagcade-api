<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AccountReportRepositoryInterface
{
    /**
     * get report for a publisher
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param AccountReportInterface $report
     * @return mixed
     */
    public function override(AccountReportInterface $report);
}