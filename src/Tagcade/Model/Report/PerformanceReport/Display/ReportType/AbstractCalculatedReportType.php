<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType;

abstract class AbstractCalculatedReportType extends AbstractReportType  {
    /**
     * @inheritdoc
     */
    public function isExpandable()
    {
        return true;
    }
} 