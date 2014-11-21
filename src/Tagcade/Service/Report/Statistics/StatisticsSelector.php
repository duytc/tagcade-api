<?php

namespace Tagcade\Service\Report\Statistics;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\Report\InvalidDateException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;
use Tagcade\Exception\RuntimeException;
use DateTime;

class StatisticsSelector implements StatisticsSelectorInterface
{
    /**
     * @var StatisticsInterface[]
     */
    protected $selectors;

    /**
     * @param StatisticsInterface[] $selectors
     */
    public function __construct(array $selectors)
    {
        $this->selectors = $selectors;
    }

    /**
     * @inheritdoc
     */
    public function getStatistics(StatisticsTypeInterface $selectorType, DateTime $startDate = null, DateTime $endDate = null, $deepLength = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {

            throw new InvalidDateException('start date must be before the end date');
        }

        if($deepLength === 0)
        {
            $deepLength = 10;
        }

        if(!is_int($deepLength) || $deepLength < 0) {

            throw new InvalidArgumentException('deepLength must be integer and positive');
        }

        /**
         * @var StatisticsInterface
         */
        $selector = $this->getSelectorFor($selectorType);

        return $selector->getStatistics($selectorType, $startDate, $endDate, $deepLength);
    }

    /**
     * @param StatisticsTypeInterface $selectorType
     * @return StatisticsInterface
     */
    protected function getSelectorFor(StatisticsTypeInterface $selectorType)
    {
        /**
         * @var StatisticsInterface $selector
         */
        foreach($this->selectors as $selector) {

            if ($selector->supportsStatisticsType($selectorType)) {
                return $selector;
            }
        }

        throw new RuntimeException('Cannot find a selector for this statistics type');
    }

}