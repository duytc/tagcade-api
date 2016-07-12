<?php


namespace Tagcade\Service\Core\Site;


use Tagcade\Model\User\Role\PublisherInterface;

interface SiteImportBulkDataInterface {
    /**
     * @param array $arrayMapSitesData
     * @param PublisherInterface $publisher
     * @param array $arrayMapDisplayAdSlot
     * @param array $dynamicAdSlots
     * @param array $expression
     * @param array $arrayMapAdTags
     * @param $dryOption
     * @return mixed
     */
    public function createSites(array $arrayMapSitesData, PublisherInterface $publisher, array $arrayMapDisplayAdSlot,
                                array $dynamicAdSlots, array $expression , array $arrayMapAdTags, $dryOption);


    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function createDataForSites($excelRows, PublisherInterface $publisher);
}
