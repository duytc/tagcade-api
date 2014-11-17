<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Doctrine\ORM\NoResultException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\CPMRateDisplayAdTagRepositoryInterface;

class RevenueCalculator implements RevenueCalculatorInterface
{
    /**
     * @var CPMRateDisplayAdTagRepositoryInterface
     */
    private $cpmRateRepository;

    function __construct(CPMRateDisplayAdTagRepositoryInterface $cpmRateRepository)
    {
        $this->cpmRateRepository = $cpmRateRepository;
    }

    /**
     * @inheritdoc
     */
    public function calculateRevenue(AdTagInterface $adTag, $opportunities)
    {
        try {
            $rate = $this->cpmRateRepository->getRateByAdTag($adTag);
        }
        catch (NoResultException $ex) {
            $rate = $adTag->getAdNetwork()->getCpmRate();
        }

        return ($rate * $opportunities);
    }

}