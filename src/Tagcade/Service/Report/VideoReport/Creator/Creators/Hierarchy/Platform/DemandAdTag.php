<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\DemandAdTagReport;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag as DemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorAbstract;

class DemandAdTag extends CreatorAbstract implements DemandAdTagInterface
{
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(DemandAdTagReportType $reportType)
    {
        $report = new DemandAdTagReport();

        $videoDemandAdTag = $reportType->getVideoDemandAdTag();

        $videoDemandAdTagData = $this->eventCounter->getVideoDemandAdTagData($videoDemandAdTag->getId(), true, $this->getDate());

        $report
            ->setDate($this->getDate())
            ->setBids($videoDemandAdTagData->getBids())
            ->setClicks($videoDemandAdTagData->getClicks())
            ->setErrors($videoDemandAdTagData->getErrors())
            ->setRequests($videoDemandAdTagData->getRequests())
            ->setImpressions($videoDemandAdTagData->getImpressions())
            ->setBlocks($videoDemandAdTagData->getBlocks())
            ->setVideoDemandAdTag($videoDemandAdTag)
        ;

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DemandAdTagReportType;
    }
}