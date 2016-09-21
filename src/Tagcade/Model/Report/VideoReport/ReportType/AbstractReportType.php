<?php

namespace Tagcade\Model\Report\VideoReport\ReportType;

abstract class AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = null;
    protected static $supportedMinBreakDown = [];

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return static::REPORT_TYPE;
    }

    public function supportMinBreakDown()
    {
        return static::$supportedMinBreakDown;
    }
}