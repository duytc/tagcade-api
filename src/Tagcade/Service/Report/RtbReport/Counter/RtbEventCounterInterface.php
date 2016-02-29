<?php

namespace Tagcade\Service\Report\RtbReport\Counter;


interface RtbEventCounterInterface
{
    /**
     * @param \DateTime|null $date
     * @return self
     */
    public function setDate(\DateTime $date = null);

    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * get Rtb AdSlot Report
     *
     * @param int $adSlotId
     *
     * @return 'slotId' => AdSlotReportCount
     */
    public function getRtbAdSlotReport($adSlotId);

    /**
     * get Rtb AdSlot Reports
     *
     * @param array|int[] $adSlotIds
     *
     * @return array ['slotId' => AdSlotReportCount]
     */
    public function getRtbAdSlotReports(array $adSlotIds);

    /**
     * get Rtb Ron AdSlot Report
     *
     * @param int $ronAdSlotId
     * @param int|null $segmentId [optional]
     * @return mixed
     */
    public function getRtbRonAdSlotReport($ronAdSlotId, $segmentId = null);

    /**
     * get Rtb Ron AdSlot Reports
     *
     * @param array $ronAdSlotIds
     * @param int|null $segmentId [optional]
     * @return mixed
     */
    public function getRtbRonAdSlotReports(array $ronAdSlotIds, $segmentId = null);
}