<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily as DailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DailyReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class Daily implements SelectorInterface
{
    /**
     * @var DailyReportRepositoryInterface
     */
    private $dailyRepository;

    function __construct(DailyReportRepositoryInterface $dailyRepository)
    {
        $this->dailyRepository = $dailyRepository;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof DailyReportType) {
            throw new InvalidArgumentException('Expect instance of DailyReportType');
        }

        return $this->dailyRepository->getReportFor($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DailyReportType;
    }
}