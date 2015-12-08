<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\AccountManagementRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AccountManagement implements SelectorInterface, PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;
    protected $defaultPageRange;

    /**
     * @var AccountManagementRepositoryInterface
     */
    protected $accMngRepository;

    function __construct(AccountManagementRepositoryInterface $accMngRepository, $defaultPageRange)
    {
        $this->accMngRepository = $accMngRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AccountManagementReportType) {
            throw new InvalidArgumentException('Expect instance of AccountManagementReportType');
        }

        $pageSize = $params->getSize() > 0 ? : $this->defaultPageRange;

        return $this->paginator->paginate(
                $this->accMngRepository->getQueryForPaginator($params),
                $params->getPage(),
                $pageSize
        );
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountManagementReportType;
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