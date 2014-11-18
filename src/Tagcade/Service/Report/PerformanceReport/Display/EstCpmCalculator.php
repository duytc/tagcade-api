<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use \DateTime;

class EstCpmCalculator implements EstCpmCalculatorInterface
{

    function __construct()
    {

    }

    public function getEstCpmForAdTag(AdTagInterface $adTag, DateTime $date = null)
    {
        $estCpm = $adTag->getAdNetwork()->getDefaultCpmRate();

        if ($adTag->getId() === 5) {
            $estCpm = 15;
        }

        if (!$estCpm) {
            return 0;
        }

        return $estCpm;
    }

    /**
     * @inheritdoc
     */
    public function calculateRevenue(AdTagInterface $adTag, $opportunities)
    {
        // check that opportunities is an integer

        $rate = $adTag->getAdNetwork()->getDefaultCpmRate();

        if ($adTag->getId() === 5) {
            $rate = 15;
        }

        if (!$rate || !$opportunities) {
            return 0;
        }

        return ($rate * ($opportunities / 1000));
    }

    public function updateCpmRateForAdTag(AdTagInterface $adTag, $cpmRate, DateTime $startDate, DateTime $endDate = null)
    {
        // update table
        //$this->cpmRateRepository->updateRateForAdTag($adTag, $cpmRate, $startDate, $endDate);
        // throw exception if problem

        return $this;
    }

    public function updateCpmRateForAdNetwork(AdNetworkInterface $adNetwork, $cpmRate, DateTime $startDate, DateTime $endDate = null)
    {
        // update table
        /**
         * @var AdTagInterface $adTag
         */
        foreach($adNetwork->getAdTags() as $adTag) {
            $this->updateCpmRateForAdTag($adTag, $cpmRate, $startDate, $endDate);
        }
        // throw exception if problem

        return $this;
    }

}