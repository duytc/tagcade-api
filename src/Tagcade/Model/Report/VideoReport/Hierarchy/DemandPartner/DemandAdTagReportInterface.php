<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner;

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
     * @return int
     */
    public function getVideoDemandAdTagId();

    /**
     * @return int
     */
    public function getVideoDemandPartnerId();

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @return mixed
     */
    public function setVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag);
}