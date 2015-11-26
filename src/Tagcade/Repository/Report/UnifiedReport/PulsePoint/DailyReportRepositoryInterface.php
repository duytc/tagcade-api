<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Doctrine\Common\Persistence\ObjectRepository;

interface DailyReportRepositoryInterface extends ObjectRepository
{
    public function getReports();
}