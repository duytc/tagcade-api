<?php

namespace Tagcade\Repository\Report\UnifiedReport;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;

interface UnifiedReportRepositoryInterface extends ObjectRepository
{
    public function getReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);
}