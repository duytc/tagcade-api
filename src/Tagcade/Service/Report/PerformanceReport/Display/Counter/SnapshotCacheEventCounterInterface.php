<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

interface SnapshotCacheEventCounterInterface
{
    //For ad tag

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotRefreshesCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotBlankImpressionCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotVoidImpressionCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotOpportunitiesCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotClicksCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotImpressionsCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotVerifyImpressionsCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotPassbacksCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotUnVerifyImpressionsCount($adTagId, $postFix);

    /**
     * @param $adTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotFirstOpportunitiesCount($adTagId, $postFix);

    //For adslot

    /**
     * @param $adSlotId
     * @param $postFix
     * @return mixed
     */
    public function snapshotSlotOpportunitiesCount($adSlotId, $postFix);
}