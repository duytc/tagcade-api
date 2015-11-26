<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\Common\Persistence\ObjectRepository;

interface AccountManagementRepositoryInterface extends ObjectRepository
{
    public function getReports();
}