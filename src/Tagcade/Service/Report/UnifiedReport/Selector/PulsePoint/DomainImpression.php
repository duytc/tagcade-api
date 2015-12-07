<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Tagcade\Exception\InvalidArgumentException;
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
    /**
     * @var DomainReportRepositoryInterface
     */
    private $domainReportRepository;

    function __construct(DomainReportRepositoryInterface $domainReportRepository)
    {
        $this->domainReportRepository = $domainReportRepository;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof DomainImpressionReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        if ($params->getSize() > 0) {
            $pagination = $this->paginator->paginate(
                $this->domainReportRepository->getQueryForPaginator($params), /* query NOT result */
                $params->getPage(),
                $params->getSize()
            );
        }
        else {
            $pagination = $this->paginator->paginate(
                $this->domainReportRepository->getQueryForPaginator($params), /* query NOT result */
                $params->getPage()
            );
        }

        return $pagination;
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