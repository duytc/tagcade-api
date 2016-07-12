<?php


namespace Tagcade\Service\Core\AdSlot;


use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayAdSlotImportBulkDataInterface {

    /**
     * @param array $allDisplayAdSlotsData
     * @param array $allAdTags
     * @param $dryOption
     * @return mixed
     */
    public function importDisplayAdSlots(array $allDisplayAdSlotsData, array $allAdTags, $dryOption);

    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function createAllDisplayAdSlotsData($excelRows, PublisherInterface $publisher );

} 