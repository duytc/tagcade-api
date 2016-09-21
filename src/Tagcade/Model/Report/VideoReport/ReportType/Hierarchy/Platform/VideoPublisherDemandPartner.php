<?php


namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform;


use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherDemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractReportType;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoPublisherDemandPartner extends AbstractReportType implements CalculatedReportTypeInterface
{
    protected $videoPublisher;
    protected $videoDemandPartner;

    /**
     * @param null $videoPublisher
     * @param null $videoDemandPartner
     */
    public function __construct($videoPublisher = null, $videoDemandPartner = null)
    {
        if ($videoPublisher instanceof VideoPublisherInterface && $videoDemandPartner instanceof VideoDemandPartnerInterface) {
            $this->videoPublisher = $videoPublisher;
            $this->videoDemandPartner = $videoDemandPartner;
        }
    }

    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner()
    {
        return $this->videoDemandPartner;
    }

    /**
     * @return null
     */
    public function getVideoPublisher()
    {
        return $this->videoPublisher;
    }


    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof DemandAdTagReportInterface;
    }

    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof PublisherDemandPartnerReportInterface;
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
        return 'videoPublisherDemandPartner';
    }
}