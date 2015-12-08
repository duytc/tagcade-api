<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DailyReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class Daily implements SelectorInterface, PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;
    protected $defaultPageRange;
    /**
     * @var DailyReportRepositoryInterface
     */
    private $dailyRepository;

    function __construct(DailyReportRepositoryInterface $dailyRepository, $defaultPageRange)
    {
        $this->dailyRepository = $dailyRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof DailyReportType) {
            throw new InvalidArgumentException('Expect instance of DailyReportType');
        }

        $pageSize = $params->getSize() > 0 ? : $this->defaultPageRange;

        return $this->paginator->paginate(
            $this->dailyRepository->getQueryForPaginator($params),
            $params->getPage(),
            $pageSize
        );
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DailyReportType;
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