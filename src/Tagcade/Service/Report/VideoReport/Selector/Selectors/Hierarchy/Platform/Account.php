<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as PlatformAccountReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoAccountReportRepository;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;

class Account extends AbstractSelector
{
    /**
     * @var VideoAccountReportRepository
     */
    private $videoAccountReportRepository;

    function __construct(VideoAccountReportRepository $videoAccountReportRepository)
    {
        $this->videoAccountReportRepository = $videoAccountReportRepository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(PlatformAccountReportType $reportType, FilterParameterInterface $filterParameter)
    {
        $publisher = $reportType->getPublisher();
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        return $this->videoAccountReportRepository->getReportsFor($publisher, $startDate, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformAccountReportType;
    }
}