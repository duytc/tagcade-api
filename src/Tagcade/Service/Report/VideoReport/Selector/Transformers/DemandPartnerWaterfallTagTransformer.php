<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Doctrine\Common\Collections\Collection;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandPartnerWaterfallTagTransformer extends AbstractTransformer implements TransformerInterface
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
        if (!$report instanceof WaterfallTagReportInterface) {
            return false;
        }

        /** @var VideoWaterfallTagItemInterface[]|Collection $adTagItems */
        $adTagItems = $report->getVideoWaterfallTag()->getVideoWaterfallTagItems();

        if ($adTagItems instanceof Collection) {
            $adTagItems = $adTagItems->toArray();
        }

        if (!is_array($adTagItems)) {
            return false;
        }

        foreach ($adTagItems as $adTagItem) {
            /** @var VideoDemandAdTagInterface[]/Collection $demandAdTags */
            $demandAdTags = $adTagItem->getVideoDemandAdTags();

            if ($demandAdTags instanceof Collection) {
                $demandAdTags = $demandAdTags->toArray();
            }

            return current($demandAdTags)->getVideoDemandPartner();
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof WaterfallTag && $breakDownParameter->getMinBreakdown() == BreakDownParameter::VIDEO_DEMAND_PARTNER_KEY);
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