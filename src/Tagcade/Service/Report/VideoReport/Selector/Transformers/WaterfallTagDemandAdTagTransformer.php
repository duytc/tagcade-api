<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\WaterfallTagReport;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandAdTagReportInterface as DemandPartnerDemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandPartnerDemandAdTag;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class WaterfallTagDemandAdTagTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @return mixed target class need be transformed to
     */
    protected function getTargetClass()
    {
        return WaterfallTagReport::class;
    }

    /**
     * @inheritdoc
     */
    protected function getReportTypeClass()
    {
        return WaterfallTag::class;
    }
    /**
     * @inheritdoc
     */
    protected function getParentObject(ReportInterface $report)
    {
        if (! ($report instanceof DemandAdTagReportInterface || $report instanceof DemandPartnerDemandAdTagReportInterface)) {
            return false;
        }

        /** @var null|VideoWaterfallTagItemInterface $adTagItem */
        $adTagItem = $report->getVideoDemandAdTag()->getVideoWaterfallTagItem();

        if (!$adTagItem instanceof VideoWaterfallTagItemInterface) {
            return false;
        }

        return $adTagItem->getVideoWaterfallTag();
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return (($reportType instanceof DemandAdTag || $reportType instanceof DemandPartnerDemandAdTag )
            && $breakDownParameter->getMinBreakdown() == BreakDownParameter::VIDEO_WATERFALL_TAG_KEY
            // for special case breakdown by both WaterfallTag and DemandPartner and WaterfallTag, we need transform DemandAdTag to DemandPartnerWaterfallTag model
            && !$breakDownParameter->hasVideoDemandPartners()
        );
    }

    /**
     * @inheritdoc
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);
        
        /**@var WaterfallTagReportInterface $parentReport */
        $parentReport->setVideoWaterfallTag($this->getParentObject($childReport));
        $parentReport->setAdTagRequests(null);
        $parentReport->setAdTagBids(null);
        $parentReport->setAdTagErrors(null);

        return $parentReport;
    }
}