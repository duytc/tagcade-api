<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Publisher;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Model\User\Role\PublisherInterface;

class Publisher extends AbstractReportType
{
    const REPORT_TYPE = 'unified.publisher';
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * Account constructor.
     * @param PublisherInterface $publisher
     */
    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    public function getPublisherId()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
        }

        return null;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof PublisherReport;
    }
}