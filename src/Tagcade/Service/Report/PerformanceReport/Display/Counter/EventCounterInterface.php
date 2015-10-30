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
     * @param int $ronSlotId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonSlotOpportunityCount($ronSlotId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getOpportunityCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonOpportunityCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getPassbackCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonPassbackCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getFirstOpportunityCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonFirstOpportunityCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVerifiedImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVerifiedImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getUnverifiedImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonUnverifiedImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getBlankImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonBlankImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVoidImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVoidImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getClickCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonClickCount($ronTagId, $segment = null);
}