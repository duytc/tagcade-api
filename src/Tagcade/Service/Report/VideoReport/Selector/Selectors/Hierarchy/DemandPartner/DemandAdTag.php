<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\DemandPartner;


use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandPartnerDemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\DemandPartner\VideoDemandAdTagReportRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;

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
    protected function doGetReports(DemandPartnerDemandAdTagReportType $reportType, FilterParameterInterface $filterParameter)
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
        return $reportType instanceof DemandPartnerDemandAdTagReportType;
    }
}