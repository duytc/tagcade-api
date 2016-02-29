<?php


namespace Tagcade\Repository\Report\RtbReport;


use Tagcade\Service\Report\RtbReport\Selector\RtbReportParams;

class WinnerRepository extends AbstractReportRepository implements WinnerRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReports(RtbReportParams $params)
    {
        // TODO: Implement getReports() method.
        return $this->getReportsInRangeQuery($params->getStartDate(), $params->getEndDate())
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getReportsByAdTagId($adTagId, RtbReportParams $params, $verified = true)
    {
        $qb = $this->getReportsInRangeQuery($params->getStartDate(), $params->getEndDate());

        return $qb
            ->andWhere('r.tagId = :tagId')
            ->andWhere('r.verified = :verified')
            ->setParameter('tagId', $adTagId)
            ->setParameter('verified', $verified)
            ->getQuery()
            ->getResult();
    }
}