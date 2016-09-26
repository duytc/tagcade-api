<?php

namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandAdTag extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'platform.videoDemandAdTag';

    /**
     * @var VideoDemandAdTagInterface
     */
    private $videoDemandAdTag;
    protected static $supportedMinBreakDown = ['videoDemandAdTag', 'day'];

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
        return ($this->videoDemandAdTag instanceof VideoDemandAdTagInterface) ? $this->videoDemandAdTag->getId() : null;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof DemandAdTagReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isSupportParams(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        $isUsingDemandAdTag = (
            (!empty($filterParameter->getVideoDemandAdTags()) || !empty($breakDownParameter->hasVideoDemandAdTags())) &&
            !$breakDownParameter->hasVideoDemandPartners()
        );

        //Breakdown or filter by Video Publisher and video DemandAdTag
        $filterByVideoPublisherAndBreakDownByDemandPartner = !empty($filterParameter->getVideoPublishers())
                                                                && $breakDownParameter->hasVideoDemandPartners()
                                                                && !$breakDownParameter->hasVideoWaterfallTags();

        $filterByDemandPartnerAndBreakDownByVideoPublisher = !empty($filterParameter->getVideoDemandPartners())
                                                              && $breakDownParameter->hasVideoPublishers();

        $breakDownByVideoPublisherAndBreakDownByVideoDemandPartner = $breakDownParameter->hasVideoPublishers()
                                                                    && $breakDownParameter->hasVideoDemandPartners()
                                                                    && !$breakDownParameter->hasVideoWaterfallTags() ;

        $filterByVideoDemandPartnerAndFilterByVideoPublisher = !empty($filterParameter->getVideoDemandPartners())
                                                                && !empty($filterParameter->getVideoPublishers())
                                                                && !$breakDownParameter->hasVideoWaterfallTags();

        return $filterByDemandPartnerAndBreakDownByVideoPublisher
            || $filterByVideoPublisherAndBreakDownByDemandPartner
            || $breakDownByVideoPublisherAndBreakDownByVideoDemandPartner
            || $filterByVideoDemandPartnerAndFilterByVideoPublisher
            || $isUsingDemandAdTag;

    }

    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
        return $this->getVideoDemandAdTagId();
    }
}