<?php

namespace Tagcade\Repository\Report\RtbReport;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Service\Report\RtbReport\Selector\RtbReportParams;

interface RTBReportRepositoryInterface extends ObjectRepository
{
    /**
     * @param RtbReportParams $params
     * @return array
     */
    public function getReports(RtbReportParams $params);
}