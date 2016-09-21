<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PublisherReport;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher as PublisherReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag as WaterfallTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\VideoReport\Creator\Creators\HasSubReportsTrait;

class Publisher extends CreatorAbstract implements PublisherInterface
{
    use HasSubReportsTrait;

    /** @var VideoWaterfallTagManagerInterface */
    protected $videoWaterfallTagManager;

    public function __construct(VideoWaterfallTagManagerInterface $videoWaterfallTagManager, WaterfallTagInterface $subReportCreator)
    {
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(PublisherReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new PublisherReport();

        $publisher = $reportType->getVideoPublisher();

        $report
            ->setVideoPublisher($publisher)
            ->setDate($this->getDate());

        $videoWaterfallTags = $this->videoWaterfallTagManager->getVideoWaterfallTagsForVideoPublisher($publisher);

        /**
         * @var VideoWaterfallTagInterface $videoWaterfallTag
         */
        foreach ($videoWaterfallTags as $videoWaterfallTag) {
            /** @var WaterfallTagReportInterface $subReport */
            $subReport = $this->subReportCreator->createReport(new WaterfallTagReportType($videoWaterfallTag));
            $report->addSubReport($subReport->setSuperReport($report));
            $report->setAdTagRequests($report->getAdTagRequests() + $subReport->getAdTagRequests());
            $report->setAdTagBids($report->getAdTagBids() + $subReport->getAdTagBids());
            $report->setAdTagErrors($report->getAdTagErrors() + $subReport->getAdTagErrors());
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PublisherReportType;
    }
}