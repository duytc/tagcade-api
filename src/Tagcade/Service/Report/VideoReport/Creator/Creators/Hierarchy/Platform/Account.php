<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\VideoPublisherManagerInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher as PublisherReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\VideoReport\Creator\Creators\HasSubReportsTrait;

class Account extends CreatorAbstract implements AccountInterface
{
    use HasSubReportsTrait;

    /** @var VideoPublisherManagerInterface */
    protected $videoPublisherManager;

    public function __construct(VideoPublisherManagerInterface $videoPublisherManager, PublisherInterface $subReportCreator)
    {
        $this->videoPublisherManager = $videoPublisherManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AccountReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new AccountReport();

        $publisher = $reportType->getPublisher();

        $report
            ->setPublisher($publisher)
            ->setDate($this->getDate());

        $videoPublishers = $this->videoPublisherManager->getVideoPublishersForPublisher($publisher);

        /**
         * @var VideoPublisherInterface $videoPublisher
         */
        foreach ($videoPublishers as $videoPublisher) {
            /** @var PublisherReportInterface $subReport */
            $subReport = $this->subReportCreator->createReport(new PublisherReportType($videoPublisher));
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
        return $reportType instanceof AccountReportType;
    }
}