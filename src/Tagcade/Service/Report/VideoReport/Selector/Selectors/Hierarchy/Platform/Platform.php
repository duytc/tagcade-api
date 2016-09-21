<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\Platform;


;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Platform as VideoPlatformReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoPlatformReportRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;

class Platform extends AbstractSelector
{
    /**
     * @var VideoPlatformReportRepositoryInterface
     */
    private $videoPlatformReportRepository;

    function __construct(VideoPlatformReportRepositoryInterface $videoPlatformReportRepository)
    {
        $this->videoPlatformReportRepository = $videoPlatformReportRepository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(ReportTypeInterface $reportType, FilterParameterInterface $filterParameter)
    {
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        return $this->videoPlatformReportRepository->getReportsFor($reportType, $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof VideoPlatformReportType;
    }
}