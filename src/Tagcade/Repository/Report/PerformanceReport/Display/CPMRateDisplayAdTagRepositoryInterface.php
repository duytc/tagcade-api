<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display;


use Doctrine\ORM\NoResultException;
use Tagcade\Model\Core\AdTagInterface;

interface CPMRateDisplayAdTagRepositoryInterface
{
    /**
     * Find rate for $adTag on current Date. Throw not result exception if not found
     * @param AdTagInterface $adTag
     * @return float
     * @throws NoResultException
     */
    public function getRateByAdTag(AdTagInterface $adTag);
} 