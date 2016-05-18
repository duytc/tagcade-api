<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\CalculatedReportTypeInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisher extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'subPublisher.subPublisher';

    /** @var SubPublisherInterface */
    private $subPublisher;

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

    /**
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not yet supported, re-modify in the future
    }
}