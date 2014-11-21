<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType;


abstract class AbstractReportType implements ReportTypeInterface {
    const REPORT_TYPE = null;

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return static::REPORT_TYPE;
    }
} 