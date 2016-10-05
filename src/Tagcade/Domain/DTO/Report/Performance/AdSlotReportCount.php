<?php

namespace Tagcade\Domain\DTO\Report\Performance;


class AdSlotReportCount implements BaseAdSlotReportCountInterface
{

    const CACHE_KEY_SLOT_OPPORTUNITY       = 'opportunities';
    const CACHE_KEY_RTB_IMPRESSION         = 'impression';
    const CACHE_KEY_HEADER_BID_REQUEST     = 'hb_bid_request';

    private $slotOpportunities = 0;
    private $rtbImpression = 0;
    private $hbRequests = 0;

    function __construct(array $reportCounts)
    {
        if (array_key_exists(self::CACHE_KEY_SLOT_OPPORTUNITY, $reportCounts)) {
            $this->slotOpportunities = (int)$reportCounts[self::CACHE_KEY_SLOT_OPPORTUNITY];
        }

        if (array_key_exists(self::CACHE_KEY_RTB_IMPRESSION, $reportCounts)) {
            $this->rtbImpression = (int)$reportCounts[self::CACHE_KEY_RTB_IMPRESSION];
        }

        if (array_key_exists(self::CACHE_KEY_HEADER_BID_REQUEST, $reportCounts)) {
            $this->hbRequests = (int)$reportCounts[self::CACHE_KEY_HEADER_BID_REQUEST];
        }
    }

    /**
     * @return mixed
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return int
     */
    public function getRtbImpression()
    {
        return $this->rtbImpression;
    }

    /**
     * @return int
     */
    public function getHbRequests()
    {
        return $this->hbRequests;
    }
}