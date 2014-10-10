<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\Behaviours;

trait HasSubReports
{
    /**
     * @var \Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\ReportTypeInterface
     */
    protected $subReportCreator;

    protected function syncEventCounterForSubReports()
    {
        $this->subReportCreator->setEventCounter($this->getEventCounter());
    }
}