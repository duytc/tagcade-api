<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\VideoReport\ReportInterface;

class PublisherReport extends AbstractCalculatedReport implements PublisherReportInterface
{
    use SuperReportTrait;

    /** @var VideoPublisherInterface */
    protected $videoPublisher;

    public function getVideoPublisher()
    {
        return $this->videoPublisher;
    }

    public function getVideoPublisherId()
    {
        if ($this->videoPublisher instanceof VideoPublisherInterface) {
            return $this->videoPublisher->getId();
        }

        return null;
    }

    public function setVideoPublisher(VideoPublisherInterface $publisher)
    {
        $this->videoPublisher = $publisher;
        return $this;
    }


    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof WaterfallTagReport;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }
}