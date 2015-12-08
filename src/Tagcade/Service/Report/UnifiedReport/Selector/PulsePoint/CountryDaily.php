<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\CountryDailyRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\CountryDaily as CountryDailyReportType;

class CountryDaily implements SelectorInterface, PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;
    protected $defaultPageRange;
    /**
     * @var CountryDailyRepositoryInterface
     */
    protected $countryDailyRepository;

    function __construct(CountryDailyRepositoryInterface $countryDailyRepository, $defaultPageRange)
    {
        $this->countryDailyRepository = $countryDailyRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof CountryDailyReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        $pageSize = $params->getSize() > 0 ? : $this->defaultPageRange;

        return $this->paginator->paginate(
            $this->countryDailyRepository->getQueryForPaginator($params),
            $params->getPage(),
            $pageSize
        );
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof CountryDailyReportType;
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