<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;

interface PublisherDemandPartnerReportInterface extends CalculatedReportInterface
{
    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner();

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     */
    public function setVideoDemandPartner($videoDemandPartner);

    /**
     * @return VideoPublisherInterface
     */
    public function getVideoPublisher();

    /**
     * @param VideoPublisherInterface $videoPublisher
     */
    public function setVideoPublisher($videoPublisher);

} 