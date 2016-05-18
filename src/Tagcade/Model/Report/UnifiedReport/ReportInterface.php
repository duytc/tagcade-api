<?php

namespace Tagcade\Model\Report\UnifiedReport;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface as BaseReportInterface;

interface ReportInterface extends BaseReportInterface, ModelInterface
{
    /**
     * @param mixed $fillRate
     * @return self
     */
    public function forceSetFillRate($fillRate);
}