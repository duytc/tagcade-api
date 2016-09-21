<?php


namespace Tagcade\Service\Report\VideoReport\Counter;


use DateTime;
use Tagcade\Domain\DTO\Report\VideoReport\VideoDemandAdTagReportDataInterface;
use Tagcade\Domain\DTO\Report\VideoReport\VideoWaterfallTagReportDataInterface;

interface VideoEventCounterInterface
{
    /**
     * @param DateTime|null $date
     * @return self
     */
    public function setDate(DateTime $date = null);

    /**
     * @return DateTime
     */
    public function getDate();

    /**
     * @param $videoWaterfallTagId
     * @param bool $supportMGet
     * @param $date = null
     * @return VideoWaterfallTagReportDataInterface
     */
    public function getVideoWaterfallTagData($videoWaterfallTagId, $supportMGet = true, $date = null);

    /**
     * @param $videoDemandAdTagId
     * @param bool $supportMGet
     * @param $date = null
     * @return VideoDemandAdTagReportDataInterface
     */
    public function getVideoDemandAdTagData($videoDemandAdTagId, $supportMGet = true, $date = null);

    /**
     * get VideoWaterfallTag Request Count
     * @param string $videoWaterfallTagId we using uuid instead of id!!!
     * @param null|DateTime $date = null
     * @return int
     */
    public function getVideoWaterfallTagRequestCount($videoWaterfallTagId, $date = null);

    /**
     * get VideoWaterfallTag Bid Count
     *
     * @param string $videoWaterfallTagId we using uuid instead of id!!!
     * @param null|DateTime $date = null
     * @return int
     */
    public function getVideoWaterfallTagBidCount($videoWaterfallTagId, $date = null);

    /**
     * get VideoWaterfallTag Error Count
     *
     * @param string $videoWaterfallTagId we using uuid instead of id!!!
     * @param null|DateTime $date = null
     * @return int
     */
    public function getVideoWaterfallTagErrorCount($videoWaterfallTagId, $date = null);

    /**
     * @param $videoDemandAdTagId
     * @param null $date
     * @return mixed
     */
    public function getVideoDemandAdTagImpressionsCount($videoDemandAdTagId, $date = null);
}