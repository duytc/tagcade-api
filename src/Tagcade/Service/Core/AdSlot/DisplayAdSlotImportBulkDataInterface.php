<?php


namespace Tagcade\Service\Core\AdSlot;


use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayAdSlotImportBulkDataInterface {

    /**
     * @param array $allDisplayAdSlotsData
     * @param Site $site
     * @param PublisherInterface $publisher
     * @param $dryOption
     * @return mixed
     */
    public function importDisplayAdSlots(array $allDisplayAdSlotsData, Site $site, PublisherInterface $publisher, $dryOption);

    /**
     * @param $excelRows
     * @param Site $site
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function createAllDisplayAdSlotsData($excelRows, Site $site, PublisherInterface $publisher);

    /**
     * @return mixed
     */
    public function getSiteNameIndex();

    /**
     * @return mixed
     */
    public function getAdSlotNameIndex();

} 