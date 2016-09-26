<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\DemandPartner;


use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\DemandPartner\VideoDemandPartnerReportRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\AbstractSelector;

class DemandPartner extends AbstractSelector
{
    /**
     * @var VideoDemandPartnerReportRepositoryInterface
     */
    private $videoDemandPartnerReportRepository;

    function __construct(VideoDemandPartnerReportRepositoryInterface $videoDemandPartnerReportRepository)
    {
        $this->videoDemandPartnerReportRepository = $videoDemandPartnerReportRepository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(DemandPartnerReportType $reportType, FilterParameterInterface $filterParameter)
    {
        $videoDemandPartner = $reportType->getVideoDemandPartner();
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        return $this->videoDemandPartnerReportRepository->getReportsFor($videoDemandPartner, $startDate, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DemandPartnerReportType;
    }
}