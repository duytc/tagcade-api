<?php


namespace Tagcade\Service\Core\AdTag;


use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdTagImportBulkDataInterface {

    /**
     * @param DisplayAdSlot $displayAdSlotObject
     * @param array $allAdTags
     * @param $dryOption
     * @return mixed
     */
    public function importAdTagsForOneAdSlot(DisplayAdSlot $displayAdSlotObject, array $allAdTags, $dryOption );

    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function createAllAdTagsData($excelRows, PublisherInterface $publisher);

    /**
     * @return mixed
     */
    public function getAdSlotNameIndex();
} 