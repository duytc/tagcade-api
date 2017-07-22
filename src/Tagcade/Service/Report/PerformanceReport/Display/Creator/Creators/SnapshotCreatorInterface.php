<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface SnapshotCreatorInterface extends CreatorInterface
{
    const CACHE_KEY_SLOT_OPPORTUNITY       = 'slot_opportunities';
    const CACHE_KEY_OPPORTUNITY            = 'opportunities';
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_IMPRESSION             = 'impressions';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const CACHE_KEY_VOID_IMPRESSION        = 'void_impressions';
    const CACHE_KEY_CLICK                  = 'clicks';
    const CACHE_KEY_PASSBACK               = 'passbacks'; // legacy name is fallbacks
    const CACHE_KEY_FORCED_PASSBACK        = 'forced_passbacks'; // not counted yet for now
    const CACHE_KEY_HEADER_BID_REQUEST     = 'hb_bid_request';

    const CACHE_KEY_IN_BANNER_REQUEST      = 'inbanner_request';
    const CACHE_KEY_IN_BANNER_IMPRESSION   = 'inbanner_impression';
    const CACHE_KEY_IN_BANNER_TIMEOUT      = 'inbanner_timeout';

    /**
     * Parse report data and set to model class
     *
     * @param ReportInterface $report
     * @param array $redisReportData
     *
     * @return void
     */
    public function parseRawReportData(ReportInterface $report, array $redisReportData);
}