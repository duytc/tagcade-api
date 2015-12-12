<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\AdTagDomainImpressionRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagDomainImpression implements SelectorInterface, PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;
    /**
     * @var AdTagDomainImpressionRepositoryInterface
     */
    private $adTagDomainImpRepository;
    /**
     * @var
     */
    private $defaultPageRange;

    function __construct(AdTagDomainImpressionRepositoryInterface $adTagDomainImpRepository, $defaultPageRange)
    {
        $this->adTagDomainImpRepository = $adTagDomainImpRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagDomainImpressionReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        $averageValues = $this->adTagDomainImpRepository->getAverageValues($reportType->getPublisher(), $params);

        $items = $this->adTagDomainImpRepository->getItems($reportType->getPublisher(), $params, $this->defaultPageRange);
        $count = $this->adTagDomainImpRepository->getCount($reportType->getPublisher(), $params);

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
        return $reportType instanceof AdTagDomainImpressionReportType;
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