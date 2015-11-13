<?php

namespace Tagcade\Domain\DTO\Report\Performance;


class AdSlotReportCount implements BaseAdSlotReportCountInterface
{

    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunities';

    private $slotOpportunities = 0;

    function __construct(array $reportCounts)
    {
        if (array_key_exists(self::CACHE_KEY_SLOT_OPPORTUNITY, $reportCounts)) {
            $this->slotOpportunities = (int)$reportCounts[self::CACHE_KEY_SLOT_OPPORTUNITY];
        }
    }

    /**
     * @return mixed
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }
} 