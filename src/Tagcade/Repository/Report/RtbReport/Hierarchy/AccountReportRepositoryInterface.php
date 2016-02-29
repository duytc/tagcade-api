<?php

namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;

interface AccountReportRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);
}