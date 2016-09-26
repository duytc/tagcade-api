<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface as PlatformDemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerWaterfallTagReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerWaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartnerWaterfallTag;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandPartnerWaterfallTagDemandAdTagTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @return mixed target class need be transformed to
     */
    protected function getTargetClass()
    {
        return DemandPartnerWaterfallTagReport::class;
    }

    /**
     * @inheritdoc
     */
    protected function getReportTypeClass()
    {
        return DemandPartnerWaterfallTag::class;
    }

    /**
     * @param ReportInterface $report
     * @return false|int|string
     * @throws \Exception
     */
    protected function getParentId(ReportInterface $report)
    {
        /** @var VideoWaterfallTagInterface $videoWaterfallTag */
        $parentObjects = $this->getParentObject($report);

        if (false == $parentObjects) {
            throw new \Exception('Can not find parent objects for this report');
        }

        $videoWaterfallTag = $parentObjects['videoWaterfallTag'];
        $videoWaterfallTagId = $videoWaterfallTag->getId();

        /** @var VideoDemandPartnerInterface $demandPartner */
        $demandPartner = $parentObjects['videoDemandPartner'];;
        $demandPartnerId = $demandPartner->getId();

        return $videoWaterfallTagId . '_' . $demandPartnerId;
    }

    /**
     * @param ReportInterface $report
     * @return array|bool|false|int
     */
    protected function getParentObject(ReportInterface $report)
    {
        $parentObjects = [];
        if (!($report instanceof DemandAdTagReportInterface || $report instanceof PlatformDemandAdTagReportInterface)) {
            return false;
        }

        /**@var VideoDemandAdTagInterface $videoDemandAdTag */
        $videoDemandAdTag = $report->getVideoDemandAdTag();

        /** @var null|VideoWaterfallTagItemInterface $adTagItem */
        $adTagItem = $videoDemandAdTag->getVideoWaterfallTagItem();
        if (!$adTagItem instanceof VideoWaterfallTagItemInterface) {
            return false;
        }

        $parentObjects['videoWaterfallTag'] = $adTagItem->getVideoWaterfallTag();
        $parentObjects['videoDemandPartner'] = $videoDemandAdTag->getVideoDemandPartner();

        return $parentObjects;
    }

    /**
     * @param ReportInterface $report
     * @return bool|\Tagcade\Model\Core\VideoDemandPartnerInterface
     */
    protected function getDemandPartnerObject(ReportInterface $report)
    {
        if (!$report instanceof DemandAdTagReportInterface) {
            return false;
        }

        /** @var null|VideoWaterfallTagItemInterface $adTagItem */
        return $report->getVideoDemandAdTag()->getVideoDemandPartner();

    }

    /**
     * check if supports ReportType And Breakdown
     *
     * @param ReportTypeInterface $reportType
     * @param BreakDownParameterInterface $breakDownParameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        $reportTypeCondition = $reportType instanceof DemandAdTag;

        $breakDownCondition = $breakDownParameter->hasVideoDemandPartners()
                              && $breakDownParameter->hasVideoWaterfallTags();

        $filterByDemandPartnerAndBreakDownByAdTag = !empty($filterParameter->getVideoDemandPartners())
                                                    && empty($filterParameter->getVideoWaterfallTags())
                                                    && empty($filterParameter->getVideoDemandAdTags())
                                                    && ($breakDownParameter->hasVideoWaterfallTags() && !$breakDownParameter->hasVideoDemandAdTags());

        $filterByAdTagAndBreakDownByDemandPartner = !empty($filterParameter->getVideoWaterfallTags())
                                                    && empty($filterParameter->getVideoDemandAdTags())
                                                    && $breakDownParameter->hasVideoDemandPartners()
                                                    && !$breakDownParameter->hasVideoDemandAdTags();

        return ($reportTypeCondition && $breakDownCondition) || $filterByDemandPartnerAndBreakDownByAdTag || $filterByAdTagAndBreakDownByDemandPartner;
    }

    /**
     * @inheritdoc
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);

        /**@var DemandPartnerWaterfallTagReportInterface $parentReport */
        $parentObjects = $this->getParentObject($childReport);
        $videoDemandPartner = $parentObjects['videoDemandPartner'];
        $videoWaterfallTag = $parentObjects['videoWaterfallTag'];

        $parentReport->setVideoDemandPartner($videoDemandPartner);
        $parentReport->setVideoWaterfallTag($videoWaterfallTag);

        return $parentReport;
    }
}