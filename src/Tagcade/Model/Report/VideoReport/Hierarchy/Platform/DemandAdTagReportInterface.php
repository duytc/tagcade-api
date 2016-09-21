<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface;
interface DemandAdTagReportInterface extends ReportInterface, SubReportInterface
{
    /**
     * @return VideoDemandAdTagInterface
     */
    public function getVideoDemandAdTag();

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @return self
     */
    public function setVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag);
    /**
     * @return int
     */
    public function getVideoDemandAdTagId();
}