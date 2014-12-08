<?php

namespace Tagcade\Service\Statistics\Provider\Behaviors;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

trait TopListFilterTrait
{
    /**
     * @param ReportResultInterface $reportCollection
     * @param $sortBy
     * @param int $limit
     * @param string $order
     * @return array
     */
    protected function topList(ReportResultInterface $reportCollection, $sortBy, $limit = 10, $order = 'DESC')
    {
        if (null === $sortBy) {
            throw new InvalidArgumentException('sort field must be defined');
        }
        
        $reports = $reportCollection->getReports();

        if (count($reports) < 2) {
            return $reports;
        }
        $reportClass = get_class(current($reports));

        try {
            $getterMethod = new \ReflectionMethod($reportClass, 'get' . ucfirst($sortBy));
        } catch (\Exception $e) {
            throw new InvalidArgumentException('sort field should have public getter method');
        }

        usort(
            $reports,
            function ($a, $b) use ($getterMethod, $order) {
                $valA = $getterMethod->invoke($a);
                $valB = $getterMethod->invoke($b);

                if ($valA === $valB) {
                    return 0;
                }

                return ($valA < $valB) ? ($order == 'ASC' ? -1 : 1) : ($order == 'ASC' ? 1 : -1);
            }
        );

        if (is_int($limit) && $limit > 0) {
            array_splice($reports, $limit);
        }

        return $reports;
    }
} 