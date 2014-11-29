<?php

namespace Tagcade\Model\Report\PerformanceReport;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

trait CalculateEstCpmTrait {

    /**
     * @param ReportInterface[] $reports
     * @return float|null
     */
    protected function calculateEstCpm(array $reports)
    {
        if (null === $reports) {
            throw new InvalidArgumentException('Expect a valid report');
        }

        if (empty($reports)) {
            return null;
        }

        /**
         * @var ReportInterface $report
         */
        $total = 0;
        $totalWeight = 0;

        foreach($reports as $report) {
            if (!$report instanceof ReportInterface) {
                throw new LogicException('Not a valid report instance');
            }

            $number = $report->getEstCpm();
            $weight = $report->getEstRevenue();
            $total += $number * $weight;
            $totalWeight += $weight;
        }

        return $this->getRatio($total, $totalWeight);
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    abstract protected function getRatio($numerator, $denominator);
} 