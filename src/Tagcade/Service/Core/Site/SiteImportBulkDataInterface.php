<?php


namespace Tagcade\Service\Core\Site;


use Tagcade\Model\User\Role\PublisherInterface;

interface SiteImportBulkDataInterface
{
    /**
     * @param array $arrayMapSitesData
     * @param PublisherInterface $publisher
     * @param $dryOption
     * @return mixed
     */

    public function createSites(array $arrayMapSitesData, PublisherInterface $publisher, $dryOption);

    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function createDataForSites($excelRows, PublisherInterface $publisher);
}