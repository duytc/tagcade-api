<?php

namespace Tagcade\Model\Report\RtbReport;

interface ReportDataInterface
{
    /**
     * @return mixed
     */
    public function getOpportunities();

    /**
     * @return mixed
     */
    public function getFillRate();

    /**
     * @return mixed
     */
    public function getImpressions();

    /**
     * @return mixed
     */
    public function getEarnedAmount();
}