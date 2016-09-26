<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandPartnerDemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandPartnerDemandAdTagTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @return mixed target class need be transformed to
     */
    protected function getTargetClass()
    {
        return DemandPartnerReport::class;
    }

    /**
     * @inheritdoc
     */
    protected function getReportTypeClass()
    {
        return DemandPartner::class;
    }
    /**
     * @inheritdoc
     */
    protected function getParentObject(ReportInterface $report)
    {
        if (!$report instanceof DemandAdTagReportInterface) {
            return false;
        }

        return $report->getVideoDemandAdTag()->getVideoDemandPartner();
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof DemandPartnerDemandAdTagReportType && $breakDownParameter->getMinBreakdown() == BreakDownParameter::VIDEO_DEMAND_PARTNER_KEY
                && empty($filterParameter->getVideoWaterfallTags() && empty($filterParameter->getVideoDemandAdTags())));
    }

    /**
     * @inheritdoc
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);
        
        /**@var DemandPartnerReportInterface $parentReport */
        $parentReport->setVideoDemandPartner($this->getParentObject($childReport));

        return $parentReport;
    }
}