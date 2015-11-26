<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Doctrine\Common\Persistence\ObjectRepository;

interface DomainReportRepositoryInterface extends ObjectRepository
{
    public function getReports();
}