<?php

namespace Tagcade\Service\Statistics\Provider\Fields;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Exception\InvalidArgumentException;

trait TopListFilter
{
    /**
     * @param CalculatedReportGroup[] $statisticsList
     * @param $sortBy
     * @param int $limit
     * @param string $order
     * @return array
     */
    protected function topList(array $statisticsList, $sortBy, $limit = 7, $order = 'ASC')
    {
        if ($statisticsList === null || !is_array($statisticsList)) {
            throw new InvalidArgumentException('statistic list array required');
        }

        if (null === $sortBy) {
            throw new InvalidArgumentException('sort field must be defined');
        }

        if (count($statisticsList) < 2) {
            return $statisticsList;
        }


        $reportClass = get_class(current($statisticsList));

        try {
            $getterMethod = new \ReflectionMethod($reportClass, 'get' . ucfirst($sortBy));
        } catch (\Exception $e) {
            throw new InvalidArgumentException('sort field should have public getter method');
        }

        usort(
            $statisticsList,
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
            array_splice($statisticsList, $limit);
        }

        return $statisticsList;
    }
} 