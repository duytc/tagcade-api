<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher as PlatformVideoPublisherReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoPublisherReportRepository;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;

class VideoPublisher extends AbstractSelector
{
    /**
     * @var VideoPublisherReportRepository
     */
    private $videoPublisherReportRepository;

    function __construct(VideoPublisherReportRepository $videoPublisherReportRepository)
    {
        $this->videoPublisherReportRepository = $videoPublisherReportRepository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(PlatformVideoPublisherReportType $reportType, FilterParameterInterface $filterParameter)
    {
        $videoPublisher = $reportType->getVideoPublisher();
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        return $this->videoPublisherReportRepository->getReportsFor($videoPublisher, $startDate, $endDate);
    }

    /**
     * check if selector supports reportType
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformVideoPublisherReportType;
    }
}