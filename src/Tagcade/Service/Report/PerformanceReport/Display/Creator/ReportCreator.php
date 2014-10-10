<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Exception;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;

class ReportCreator implements ReportCreatorInterface
{
    protected $reportTypes;

    /**
     * @var EventCounterInterface
     */
    protected $eventCounter;

    protected $date;

    /**
     * @param ReportTypeInterface[] $reportTypes
     * @param EventCounterInterface $eventCounter
     */
    public function __construct(array $reportTypes, EventCounterInterface $eventCounter)
    {
        foreach($reportTypes as $name => $type)
        {
            $this->addReportType($name, $type);
            unset($name, $type);
        }

        $this->eventCounter = $eventCounter;

        $this->setDate(new DateTime('today'));
    }

    /**
     * @inheritdoc
     */
    public function addReportType($name, ReportTypeInterface $reportType)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('report type name should be a string');
        }

        if ($this->reportTypeExists($name)) {
            throw new Exception(sprintf('The report type "%s" already exists', $name));
        }

        $this->reportTypes[$name] = $reportType;
    }

    /**
     * @inheritdoc
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        $this->eventCounter->setDate($date);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function getReport($name, $parameter)
    {
        if (!$this->reportTypeExists($name))
        {
            throw new InvalidArgumentException('that report type does not exist');
        }

        try {
            /** @var ReportTypeInterface $reportType */
            $reportType = $this->reportTypes[$name];

            $reportType->setEventCounter($this->eventCounter);
            $report = $reportType->createReport($parameter);
        } catch(InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception('Could get the report');
        }

        // very important!!!
        // will set off a chain reaction and calculate all fields for the entire report graph
        $report->setCalculatedFields();

        return $report;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function reportTypeExists($name)
    {
        return isset($this->reportTypes[$name]);
    }
}