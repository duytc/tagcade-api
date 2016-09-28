<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;

interface WaterfallTagReportInterface extends CalculatedReportInterface, SuperReportInterface, SubReportInterface
{
    /**
     * @return VideoWaterfallTagInterface
     */
    public function getVideoWaterfallTag();

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return self
     */
    public function setVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag);

    /**
     * @return int
     */
    public function getVideoWaterfallTagId();

    /**
     * @return float
     */
    public function getCustomRate();

    /**
     * @param float $customRate
     */
    public function setCustomRate($customRate);
} 