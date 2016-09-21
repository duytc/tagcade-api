<?php

namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandAdTagReportInterface as DemandPartnerDemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandAdTag extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'demandPartner.videoDemandAdTag';
    protected static $supportedMinBreakDown = ['videoDemandAdTag', 'day'];

    /**
     * @var VideoDemandAdTagInterface
     */
    private $videoDemandAdTag;

    public function __construct($videoDemandAdTag = null)
    {
        if ($videoDemandAdTag instanceof VideoDemandAdTagInterface) {
            $this->videoDemandAdTag = $videoDemandAdTag;
        }
    }

    /**
     * @return VideoDemandAdTagInterface
     */
    public function getVideoDemandAdTag()
    {
        return $this->videoDemandAdTag;
    }

    /**
     * @return int|null
     */
    public function getVideoDemandAdTagId()
    {
        if ($this->videoDemandAdTag instanceof VideoDemandAdTagInterface) {
            return $this->videoDemandAdTag->getId();
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getVideoDemandPartnerId()
    {
        if ($this->videoDemandAdTag instanceof VideoDemandAdTagInterface) {
            return $this->videoDemandAdTag->getVideoDemandPartner()->getId();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof DemandPartnerDemandAdTagReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isSupportParams(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        $isUsingAdSource = (
            // DemandAdTag is lowest filter or breakdown
            (!empty($filterParameter->getVideoDemandAdTags()) || $breakDownParameter->hasVideoDemandAdTags())
            // and not breakdown by VideoWaterfallTags
            && !$breakDownParameter->hasVideoWaterfallTags()
            // and breakdown by VideoDemandPartner
            && $breakDownParameter->hasVideoDemandPartners()
            // and not care breakdown by Publisher
        );

        // or breakdown by VideoWaterfallTags and VideoDemandPartner
        $isBreakdownByAdTagAndDemandPartner = ($breakDownParameter->hasVideoWaterfallTags() && $breakDownParameter->hasVideoDemandPartners());
        $isFilterByAdTagAndDemandPartner = ($filterParameter->getVideoWaterfallTags() && $filterParameter->getVideoDemandPartners());
        $isBreakDownByAdTagAndFilterByDemandPartner = $breakDownParameter->hasVideoWaterfallTags() && $filterParameter->getVideoDemandPartners();
        $isBreakDownByDemandPartnerAndFilterByAdTag = $breakDownParameter->hasVideoDemandPartners() && $filterParameter->getVideoWaterfallTags();

        return $isUsingAdSource || $isBreakdownByAdTagAndDemandPartner || $isFilterByAdTagAndDemandPartner ||
                $isBreakDownByAdTagAndFilterByDemandPartner || $isBreakDownByDemandPartnerAndFilterByAdTag;
    }

    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
       return $this->getVideoDemandAdTagId();
    }
}