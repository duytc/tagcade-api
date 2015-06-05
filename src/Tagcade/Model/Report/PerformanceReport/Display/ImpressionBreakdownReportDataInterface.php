<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;


interface ImpressionBreakdownReportDataInterface extends ReportDataInterface {

    /**
     * @return mixed
     */
    public function getBlankImpressions();

    /**
     * @return mixed
     */
    public function getFirstOpportunities();

    /**
     * @return mixed
     */
    public function getUnverifiedImpressions();

    /**
     * @return mixed
     */
    public function getVerifiedImpressions();

} 