<?php


namespace Tagcade\Repository\Report\UnifiedReport\AdMeta;

use Doctrine\Common\Persistence\ObjectRepository;

interface TotalReportRepositoryInterface extends ObjectRepository
{
    public function getReports();
}