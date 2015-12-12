<?php


namespace Tagcade\Repository\Report\UnifiedReport;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

abstract class AbstractReportRepository extends EntityRepository
{
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE);
    }

    /**
     * Convert CamelCase to Underscore
     * @param $input
     * @return string
     */
    protected function underscoreTransform($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param $field
     * @param $direction
     * @param $underscoreTransform
     * @return string
     */
    protected function appendOrderBy($field, $direction, $underscoreTransform = true)
    {
        return sprintf(" ORDER BY %s %s", $underscoreTransform ? $this->underscoreTransform($field) : $field, $direction);
    }
}