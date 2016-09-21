<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\DemandPartner;

use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\VideoReport\Creator\Creators\HasSubReportsTrait;

class DemandPartner extends CreatorAbstract implements DemandPartnerInterface
{
    use HasSubReportsTrait;

    /**
     * @var VideoDemandAdTagManagerInterface
     */
    protected $videoDemandAdTagManager;

    public function __construct(VideoDemandAdTagManagerInterface $videoDemandAdTagManager, DemandAdTagInterface $subReportCreator)
    {
        $this->videoDemandAdTagManager = $videoDemandAdTagManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(DemandPartnerReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new DemandPartnerReport();

        $videoDemandPartner = $reportType->getVideoDemandPartner();

        $report
            ->setVideoDemandPartner($videoDemandPartner)
            ->setDate($this->getDate());

        $videoDemandAdTags = $this->videoDemandAdTagManager->getVideoDemandAdTagsForDemandPartner($videoDemandPartner);

        /**
         * @var VideoDemandAdTagInterface $videoDemandAdTag
         */
        foreach ($videoDemandAdTags as $videoDemandAdTag) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new DemandAdTagReportType($videoDemandAdTag))
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DemandPartnerReportType;
    }
}