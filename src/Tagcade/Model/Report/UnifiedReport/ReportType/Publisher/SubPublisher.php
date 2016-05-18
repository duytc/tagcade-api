<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Publisher;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisher extends AbstractReportType
{
    const REPORT_TYPE = 'unified.subpublisher';
    /**
     * @var SubPublisherInterface
     */
    private $subPublisher;

    /**
     * Account constructor.
     * @param SubPublisherInterface $subPublisher
     */
    public function __construct(SubPublisherInterface $subPublisher)
    {
        $this->subPublisher = $subPublisher;
    }

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherReport;
    }
}