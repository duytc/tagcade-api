<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;

use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoDemandAdTagReportRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag as PlatformAdSourceReportType;

class DemandAdTag extends AbstractSelector
{
    /**
     * @var VideoDemandAdTagReportRepositoryInterface
     */
    private $videoDemandAdTagReportRepository;

    function __construct(VideoDemandAdTagReportRepositoryInterface $videoDemandAdTagReportRepository)
    {
        $this->videoDemandAdTagReportRepository = $videoDemandAdTagReportRepository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(PlatformAdSourceReportType $reportType, FilterParameterInterface $filterParameter)
    {
        $videoDemandAdTag = $reportType->getVideoDemandAdTag();
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        return $this->videoDemandAdTagReportRepository->getReportsFor($videoDemandAdTag, $startDate, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformAdSourceReportType;
    }
}