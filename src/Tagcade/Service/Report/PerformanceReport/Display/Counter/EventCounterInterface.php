<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;

interface EventCounterInterface
{
    /**
     * @param DateTime|null $date
     * @return self
     */
    public function setDate(DateTime $date = null);

    /**
     * @return DateTime
     */
    public function getDate();

    /**
     * @param int $slotId
     * @return mixed
     */
    public function getSlotOpportunityCount($slotId);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getOpportunityCount($tagId);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getImpressionCount($tagId);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getPassbackCount($tagId);
}