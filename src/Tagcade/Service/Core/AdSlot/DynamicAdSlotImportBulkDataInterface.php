<?php


namespace Tagcade\Service\Core\AdSlot;


use Tagcade\Entity\Core\Site;
use Tagcade\Model\User\Role\PublisherInterface;

interface DynamicAdSlotImportBulkDataInterface {
    /**
     * @param array $dynamicAdSlots
     * @param Site $siteObject
     * @param $displayAdSlotsObject
     * @param $dryOption
     * @return mixed
     */
    public function importDynamicAdSlots(array $dynamicAdSlots, Site $siteObject, $displayAdSlotsObject, $dryOption );

    /**
     * @return mixed
     */
    public function getSiteNameIndexOfDynamicAdSlot();

    /**
     * @param $expressionTargeting
     * @return mixed
     */
    public function convertExpressionTargetingToArray($expressionTargeting);

    /**
     * @return mixed
     */
    public function getNameIndexOfDynamicAdSlot();
} 