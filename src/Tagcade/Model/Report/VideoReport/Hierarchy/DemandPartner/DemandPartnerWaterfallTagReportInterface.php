<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner;

use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;
interface DemandPartnerWaterfallTagReportInterface extends ReportInterface, SuperReportInterface
{
    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner();

    /**
     * @return int|null
     */
    public function getVideoDemandPartnerId();

    /**
     * @param VideoDemandPartnerInterface $demandPartner
     * @return self
     */
    public function setVideoDemandPartner(VideoDemandPartnerInterface $demandPartner);

    /**
     * @return VideoWaterfallTagInterface
     */
    public function getVideoWaterfallTag();

    /**
     * @return int|null
     */
    public function getVideoWaterfallTagId();

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return self
     */
    public function setVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag);
}