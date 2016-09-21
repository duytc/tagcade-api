<?php

namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class WaterfallTag extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'platform.waterfallTag';
    const  SUPPORTED_MIN_BREAK_DOWN = 'waterfallTag';

    /**
     * @var VideoWaterfallTagInterface
     */
    private $videoWaterfallTag;
    protected static $supportedMinBreakDown = ['waterfallTag', 'day'];

    public function __construct($videoWaterfallTag = null)
    {
        if ($videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            $this->videoWaterfallTag = $videoWaterfallTag;
        }
    }

    /**
     * @return VideoWaterfallTagInterface
     */
    public function getVideoWaterfallTag()
    {
        return $this->videoWaterfallTag;
    }

    /**
     * @return int|null
     */
    public function getVideoWaterfallTagId()
    {
        return $this->videoWaterfallTag->getId();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof WaterfallTagReportInterface;
    }

    /**
     * check if Supports Params and Breakdowns
     *
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function isSupportParams(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        $adTagIsLowestFilter                    =  !empty($filterParameter->getVideoWaterfallTags())
                                                    && empty($filterParameter->getVideoDemandAdTags())
                                                    && empty($filterParameter->getVideoDemandPartners());

        $notBreakDownByDemandPartnerAndAdSource =   !$breakDownParameter->hasVideoDemandPartners()
                                                    && !$breakDownParameter->hasVideoDemandAdTags();

        $breakDownByAdTagAndNotFilterByDemandPartner = $breakDownParameter->hasVideoWaterfallTags()
                                                       && !$breakDownParameter->hasVideoDemandPartners()
                                                       && !$breakDownParameter->hasVideoDemandAdTags()
                                                       && empty($filterParameter->getVideoDemandPartners());

        return ($adTagIsLowestFilter && $notBreakDownByDemandPartnerAndAdSource) || $breakDownByAdTagAndNotFilterByDemandPartner;
    }

    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
        return $this->getVideoWaterfallTagId();
    }
}