<?php


namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner;


use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerWaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandPartnerWaterfallTag extends AbstractReportType implements ReportTypeInterface
{

    /**
     * @var null
     */
    private $videoWaterfallTag;
    private $videoDemandPartner;

    /**
     * @param null $videoDemandPartner
     * @param null $videoWaterfallTag
     */
    public function __construct($videoDemandPartner = null, $videoWaterfallTag = null)
    {
        if ($videoDemandPartner instanceof VideoDemandPartnerInterface && $videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            $this->videoDemandPartner = $videoDemandPartner;
            $this->videoWaterfallTag = $videoWaterfallTag;
        }
    }

    /**
     * @return null
     */
    public function getVideoWaterfallTag()
    {
        return $this->videoWaterfallTag;
    }

    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner()
    {
        return $this->videoDemandPartner;
    }

    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof DemandPartnerWaterfallTagReportInterface;
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
        return false;
    }

    /**
     * Get id of video objec id (video ad source, video ad tag, video demand partner, publisher)
     * @return mixed
     */
    public function getVideoObjectId()
    {
        return 'videoDemandPartnerWaterfallTag';
    }
}