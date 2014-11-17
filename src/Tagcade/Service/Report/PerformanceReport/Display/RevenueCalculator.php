<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Doctrine\ORM\NoResultException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\CPMRateRepositoryInterface;

class RevenueCalculator implements RevenueCalculatorInterface
{
    /**
     * @var CPMRateRepositoryInterface
     */
    private $cpmRateRepository;

    function __construct(CPMRateRepositoryInterface $cpmRateRepository)
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