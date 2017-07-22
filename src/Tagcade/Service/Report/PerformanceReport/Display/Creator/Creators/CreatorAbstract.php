<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators;

use Psr\Log\LoggerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;

abstract class CreatorAbstract implements CreatorInterface
{
    /**
     * @var EventCounterInterface|null
     */
    protected $eventCounter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->getEventCounter()->getDate();
    }

    /**
     * @inheritdoc
     */
    public function setEventCounter(EventCounterInterface $eventCounter)
    {
        $this->eventCounter = $eventCounter;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEventCounter()
    {
        if (!$this->hasEventCounter()) {
            throw new RuntimeException('eventCounter was not set');
        }

        return $this->eventCounter;
    }

    protected function hasEventCounter()
    {
        return $this->eventCounter instanceof EventCounterInterface;
    }

    /**
     * @inheritdoc
     */
    public function createReport(ReportTypeInterface $reportType)
    {
        $this->validateReportType($reportType);

        return $this->doCreateReport($reportType);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return ReportInterface|SubReportInterface
     */
    public abstract function doCreateReport(ReportTypeInterface $reportType);

    /**
     * validate report type
     * @param ReportTypeInterface $reportType
     */
    protected function validateReportType(ReportTypeInterface $reportType)
    {
        if (!$this->supportsReportType($reportType)) {
            throw new InvalidArgumentException(sprintf('Not support report type', $reportType->getReportType()));
        }
    }
}