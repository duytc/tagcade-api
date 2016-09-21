<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag as WaterfallTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoWaterfallTagReportRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;

class WaterfallTag extends AbstractSelector
{
    /**
     * @var VideoWaterfallTagReportRepositoryInterface
     */
    private $videoWaterfallTagReportRepository;
    
    function __construct(VideoWaterfallTagReportRepositoryInterface $videoWaterfallTagReportRepository)
    {
        $this->videoWaterfallTagReportRepository = $videoWaterfallTagReportRepository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(WaterfallTagReportType $reportType, FilterParameterInterface $filterParameter)
    {
        $videoWaterfallTag = $reportType->getVideoWaterfallTag();
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        return $this->videoWaterfallTagReportRepository->getReportsFor($videoWaterfallTag, $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof WaterfallTagReportType;
    }
}