<?php

namespace Tagcade\Model\Report\RtbReport\ReportType;


abstract class  AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = null;

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return static::REPORT_TYPE;
    }
}