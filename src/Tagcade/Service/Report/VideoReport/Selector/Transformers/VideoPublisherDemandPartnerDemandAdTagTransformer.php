<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherDemandPartnerReport as PlatformVideoPublisherDemandPartnerReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherDemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\VideoPublisherDemandPartner as PlatformVideoPublisherDemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoPublisherDemandPartnerDemandAdTagTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    protected function getTargetClass()
    {
        return PlatformVideoPublisherDemandPartnerReport::class;
    }

    /**
     * @inheritdoc
     */
    protected function getReportTypeClass()
    {
        return PlatformVideoPublisherDemandPartnerReportType::class;
    }

    /**
     * @param ReportInterface $report
     * @return false|int|string
     */
    protected function getParentId(ReportInterface $report)
    {
        /** @var VideoPublisherInterface $videoPublisher */
        $parentObjects = $this->getParentObject($report);
        $videoPublisher = $parentObjects['videoPublisher'];
        $videoPublisherId = $videoPublisher->getId();

        /** @var VideoDemandPartnerInterface $videoDemandPartner */
        $videoDemandPartner = $parentObjects['videoDemandPartner'];;
        $demandPartnerId = $videoDemandPartner->getId();

        return $videoPublisherId . '_' . $demandPartnerId;
    }

    /**
     * @param ReportInterface $report
     * @return array|bool|false|int
     */
    protected function getParentObject(ReportInterface $report)
    {
        $parentObjects = [];
        if (!$report instanceof DemandAdTagReportInterface) {
            return false;
        }

        $videoDemandPartner = $report->getVideoDemandAdTag()->getLibraryVideoDemandAdTag()->getVideoDemandPartner();
        $videoPublisher = $report->getVideoDemandAdTag()->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getVideoPublisher();

        $parentObjects['videoPublisher'] = $videoPublisher;
        $parentObjects['videoDemandPartner'] = $videoDemandPartner;

        return $parentObjects;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        $reportTypeCondition = $reportType instanceof DemandAdTag;

        $filterByVideoPublisherAndBreakDownByDemandPartner = !empty($filterParameter->getVideoPublishers())
                                                              && $breakDownParameter->hasVideoDemandPartners()
                                                              && !$breakDownParameter->hasVideoWaterfallTags();

        $filterByDemandPartnerAndBreakDownByVideoPublisher = !empty($filterParameter->getVideoDemandPartners())
                                                              && $breakDownParameter->hasVideoPublishers();

        $breakDownByVideoPublisherAndBreakDownByVideoDemandPartner = $breakDownParameter->hasVideoPublishers()
                                                              && $breakDownParameter->hasVideoDemandPartners()
                                                              && !$breakDownParameter->hasVideoWaterfallTags()  ;

        return ($filterByDemandPartnerAndBreakDownByVideoPublisher
            || $filterByVideoPublisherAndBreakDownByDemandPartner
            || $breakDownByVideoPublisherAndBreakDownByVideoDemandPartner) && $reportTypeCondition;

    }

    /**
     * @inheritdoc
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);

        /** @var VideoPublisherInterface $videoPublisher */
        $parentObjects = $this->getParentObject($childReport);
        $videoPublisher = $parentObjects['videoPublisher'];

        /** @var VideoDemandPartnerInterface $videoDemandPartner */
        $videoDemandPartner = $parentObjects['videoDemandPartner'];;

        /** @var PublisherDemandPartnerReportInterface $parentReport */
        $parentReport->setVideoDemandPartner($videoDemandPartner);
        $parentReport->setVideoPublisher($videoPublisher);

        return $parentReport;
    }
}