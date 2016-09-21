<?php


namespace Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner;


use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandAdTagReportInterface as DemandPartnerVideoDemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\AbstractCalculatedReportType;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class DemandPartner extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'demandPartner.demandPartner';

    /** @var VideoDemandPartnerInterface */
    protected $videoDemandPartner;
    protected static $supportedMinBreakDown = ['demandPartner', 'day'];

    public function __construct($videoDemandPartner = null)
    {
        if ($videoDemandPartner instanceof VideoDemandPartnerInterface) {
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
     * @return int|null
     */
    public function getVideoDemandPartnerId()
    {
        if ($this->videoDemandPartner instanceof VideoDemandPartnerInterface) {
            return $this->videoDemandPartner->getId();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof DemandPartnerVideoDemandAdTagReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof DemandPartnerReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isSupportParams(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        return (
            // DemandPartner is lowest filter or breakdown
            (!empty($filterParameter->getVideoDemandPartners()) || $breakDownParameter->hasVideoDemandPartners())
            // and not breakdown by VideoWaterfallTags
            && (empty($filterParameter->getVideoWaterfallTags()) && !$breakDownParameter->hasVideoWaterfallTags())
            // and not breakdown by VideoWaterfallTags
            && (empty($filterParameter->getVideoDemandAdTags()) && !$breakDownParameter->hasVideoDemandAdTags())
            // and not care breakdown by Publisher
            && (empty($filterParameter->getVideoPublishers()) && !$breakDownParameter->hasVideoPublishers())
        );
    }


    /**
     * @inheritdoc
     */
    public function getVideoObjectId()
    {
        return $this->getVideoDemandPartnerId();
    }
}