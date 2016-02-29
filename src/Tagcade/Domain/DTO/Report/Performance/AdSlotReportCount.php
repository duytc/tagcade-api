<?php

namespace Tagcade\Domain\DTO\Report\Performance;


class AdSlotReportCount implements BaseAdSlotReportCountInterface
{

    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunities';
    const CACHE_KEY_RTB_IMPRESSION         = 'impression';

    private $slotOpportunities = 0;
    private $rtbImpression = 0;

    function __construct(array $reportCounts)
    {
        if (array_key_exists(self::CACHE_KEY_SLOT_OPPORTUNITY, $reportCounts)) {
            $this->slotOpportunities = (int)$reportCounts[self::CACHE_KEY_SLOT_OPPORTUNITY];
        }

        if (array_key_exists(self::CACHE_KEY_RTB_IMPRESSION, $reportCounts)) {
            $this->rtbImpression = (int)$reportCounts[self::CACHE_KEY_RTB_IMPRESSION];
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


} 