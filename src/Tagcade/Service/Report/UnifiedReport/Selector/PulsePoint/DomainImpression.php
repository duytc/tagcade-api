<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DomainReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;

class DomainImpression implements SelectorInterface, PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;
    protected $defaultPageRange;
    /**
     * @var DomainReportRepositoryInterface
     */
    private $domainReportRepository;

    function __construct(DomainReportRepositoryInterface $domainReportRepository, $defaultPageRange)
    {
        $this->domainReportRepository = $domainReportRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof DomainImpressionReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        $averageValues = $this->domainReportRepository->getAverageValues($reportType->getPublisher(), $params);

        $items = $this->domainReportRepository->getItems($reportType->getPublisher(), $params, $this->defaultPageRange);
        $count = $this->domainReportRepository->getCount($reportType->getPublisher(), $params);

        $pagination =  $this->paginator->paginate(
            new CompoundResult($items, $count)
        );

        return array(
            'pagination' => $pagination,
            'avg' => $averageValues
        );
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DomainImpressionReportType;
    }

    /**
     * Sets the KnpPaginator instance.
     *
     * @param Paginator $paginator
     *
     * @return mixed
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }
}