<?php

namespace Tagcade\Model\Report\PerformanceReport;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

trait CalculateWeightedValueTrait
{
    /**
     * @param array $reports
     * @param string $frequencyField
     * @param string $weightField
     * @return float|null
     */
    protected function calculateWeightedValue(array $reports, $frequencyField = 'estCpm', $weightField = 'EstRevenue')
    {
        if (null === $reports) {
            throw new InvalidArgumentException('Expect a valid report');
        }

        if (empty($reports)) {
            return null;
        }

        $reportClass = get_class(current($reports));

        try {
            $getterFrequencyMethod = new \ReflectionMethod($reportClass, 'get' . ucfirst($frequencyField));
            $getterWeightMethod = new \ReflectionMethod($reportClass, 'get' . ucfirst($weightField));
        } catch (\Exception $e) {
            throw new InvalidArgumentException('frequency and weight field should have public getter methods');
        }

        /**
         * @var ReportInterface $report
         */
        $total = 0;
        $totalWeight = 0;

        foreach($reports as $report) {
            $number = $getterFrequencyMethod->invoke($report);
            $weight = $getterWeightMethod->invoke($report);
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