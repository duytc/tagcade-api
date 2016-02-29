<?php


namespace Tagcade\Repository\Report\RtbReport;

use Tagcade\Service\Report\RtbReport\Selector\RtbReportParams;

interface WinnerRepositoryInterface extends RTBReportRepositoryInterface
{
    /**
     * get Reports By AdTagId with verify status (default 'verified = true') in date range
     *
     * @param $adTagId
     * @param RtbReportParams $params
     * @param bool $verified [option] shows that winner is verified by EventCounter system
     * @internal param RtbReportParams $params
     * @return mixed
     */
    public function getReportsByAdTagId($adTagId, RtbReportParams $params, $verified = true);
}