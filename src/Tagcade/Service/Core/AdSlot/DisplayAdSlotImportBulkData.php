<?php


namespace Tagcade\Service\Core\AdSlot;


use LibraryAdSlot;
use Monolog\Logger;
use Tagcade\DomainManager\DisplayAdSlotManager;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\LibraryAdSlotManager;
use Tagcade\DomainManager\LibraryAdSlotManagerInterface;
use Tagcade\DomainManager\LibraryDisplayAdSlotManagerInterface;
use Tagcade\DomainManager\SiteManager;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Core\AdTag\AdTagImportBulkDataInterface;

class DisplayAdSlotImportBulkData implements  DisplayAdSlotImportBulkDataInterface {

    const SITE_NAME_KEY                = 'site';
    const AD_SLOT_NAME_KEY             = 'name';
    const WIDTH_KEY                    = 'width';
    const HEIGHT_KEY                   = 'height';
    const AUTO_FIT_KEY                 = 'autoFit';
    const HEADER_BID_PRICE_KEY         = 'hbBidPrice';
    const FLOOR_PRICE_KEY              = 'floorPrice';
    const RTB_STATUS_KEY               = 'rtbStatus';
    const PASS_BACK_MODE_KEY           = 'passbackMode';
    const VISIBLE_KEY                  = 'visible';
    const TYPE_KEY                     = 'type';
    const PUBLISHER_KEY                = 'publisher';
    const LIBRARY_AD_SLOT_KEY          = 'libraryAdSlot';
    const SLOT_TYPE_KEY                = 'slotType';

    const AD_SLOT_NAME_DEFAULT_VALUE        = 'adSlotName';
    const WIDTH_DEFAULT_VALUE               = null;
    const HEIGHT_DEFAULT_VALUE              = null;
    const AUTO_FIT_DEFAULT_VALUE            = false ;
    const HEADER_BID_PRICE_DEFAULT_VALUE    = null;
    const FLOOR_PRICE_DEFAULT_VALUE         = null;
    const RTB_STATUS_DEFAULT_VALUE          = 2;
    const PASS_BACK_MODE_DEFAULT_VALUE      = 'position';

    const VISIBLE_DEFAULT_VALUE         = false;
    const RTB_DEFAULT_VALUE             = 2;

    const AD_SLOT_KEY_OF_AD_TAG         = 'adSlot';

    /**
     * @var
     */
    private $adSlotsConfigs;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var AdTagImportBulkDataInterface
     */
    private $importBulkAdTagService;
    /**
     * @var LibraryAdSlotManagerInterface
     */
    private $libraryAdSlotManager;

    /**
     * @param AdTagImportBulkDataInterface $importBulkAdTagService
     * @param Logger $logger
     * @param $adSlotsConfigs
     * @param LibraryAdSlotManager $libraryAdSlotManager
     */
    function __construct(AdTagImportBulkDataInterface $importBulkAdTagService, Logger $logger, $adSlotsConfigs, LibraryAdSlotManager $libraryAdSlotManager)
    {
        $this->logger = $logger;
        $this->adSlotsConfigs = $adSlotsConfigs;
        $this->importBulkAdTagService = $importBulkAdTagService;
        $this->libraryAdSlotManager = $libraryAdSlotManager;
    }

    /**
     * @param array $allDisplayAdSlotsData
     * @param Site $site
     * @param PublisherInterface $publisher
     * @param $dryOption
     * @return array|mixed
     */
    public function importDisplayAdSlots(array $allDisplayAdSlotsData, Site $site ,PublisherInterface $publisher, $dryOption)
    {
        $displayAdSlotObjects = [];
        foreach ($allDisplayAdSlotsData as $displayAdSlot) {
            $expectAdTags = $displayAdSlot['adTags'];
            unset($displayAdSlot['adTags']);

            $displayAdSlot = $this->createDisplayAdSlotDataFromExcelRow($displayAdSlot, $site, $publisher );
            $displayAdSlotObject    = DisplayAdSlot::createDisplayAdSlotFromArray($displayAdSlot);

            $adTagObjects           = $this->importBulkAdTagService->importAdTagsForOneAdSlot($displayAdSlotObject, $expectAdTags, $dryOption);
            $displayAdSlotObject->setAdTags($adTagObjects);

            $displayAdSlotObjects[] = $displayAdSlotObject;
        }

        if (true == $dryOption) {
            $this->logger->info(sprintf('       Total display ad slots import: %d', count($displayAdSlotObjects)));
        }

        return $displayAdSlotObjects;
    }

    /**
     * @param $displayAdSlotName
     * @param array $allAdTags
     * @return array
     * @throws \Exception
     */
    protected function getAdTagsForOneAdSlot($displayAdSlotName, array $allAdTags)
    {
        $expectAdTags = [];
        foreach ($allAdTags as $adTag) {
            if (0 == strcmp($displayAdSlotName, $adTag[self::AD_SLOT_KEY_OF_AD_TAG])) {
                $expectAdTags [] = $adTag;
            }
        }

        return $expectAdTags;
    }

    /**
     * @param $excelRows
     * @param Site $site
     * @param PublisherInterface $publisher
     * @return array|mixed
     */
    public function createAllDisplayAdSlotsData($excelRows, Site $site, PublisherInterface $publisher )
    {
        $allDisplayAdSlotsData = [];

        foreach($excelRows as  $excelRow) {
            $displayAdSlotsData = $this->createDisplayAdSlotDataFromExcelRow($excelRow, $site ,$publisher);
            $allDisplayAdSlotsData[] = $displayAdSlotsData;
        }

        return $allDisplayAdSlotsData;
    }

    /**
     * @param $excelRow
     * @param Site $site
     * @param PublisherInterface $publisher
     * @return array
     * @throws \Exception
     */

    protected function createDisplayAdSlotDataFromExcelRow($excelRow, Site $site,  PublisherInterface $publisher)
    {
        $slotTypeValue          = $this->getSlotTypeValue();
        $floorPriceValue        = $this->getFloorPriceValue($excelRow);
        $hbPriceValue           = $this->getHbPriceValue($excelRow);
        $rtbStatusValue         = $this->getRtbStatusValue($excelRow);

        $libraryDisplayAdSlotData = $this->createDataForLibraryDisplayAdSlotFromExcelRow($excelRow, $publisher);

        $libraryAdSlotName = $libraryDisplayAdSlotData[self::AD_SLOT_NAME_KEY];
        $libraryDisplayAdSlot = $this->libraryAdSlotManager->getLibraryAdSlotByName($libraryAdSlotName);

        if (empty($libraryDisplayAdSlot) || (count($libraryDisplayAdSlot) > 1)) {
            $libraryDisplayAdSlotObject = LibraryDisplayAdSlot::createLibraryDisplayAdSlotFromArray($libraryDisplayAdSlotData);
        } else {
            /** @var LibraryDisplayAdSlot $libraryDisplayAdSlotObject */
            $libraryDisplayAdSlotObject = array_shift($libraryDisplayAdSlot);
            $libraryDisplayAdSlotObject->setVisible(true);
        }

        $displayAdSlot = [];
        $displayAdSlot[self::SITE_NAME_KEY]         = $site;
        $displayAdSlot[self::SLOT_TYPE_KEY]         = $slotTypeValue;
        $displayAdSlot[self::FLOOR_PRICE_KEY]       = $floorPriceValue;
        $displayAdSlot[self::HEADER_BID_PRICE_KEY]  = $hbPriceValue;
        $displayAdSlot[self::RTB_STATUS_KEY]        =  $rtbStatusValue;
        $displayAdSlot[self::LIBRARY_AD_SLOT_KEY]   = $libraryDisplayAdSlotObject;

        return $displayAdSlot;
    }

    /**
     * @param $excelRow
     * @param PublisherInterface $publisher
     * @return array
     */
    protected function createDataForLibraryDisplayAdSlotFromExcelRow($excelRow, PublisherInterface $publisher)
    {
        $adSlotNameValue    = $this->getAdSlotNameValue($excelRow);
        $visibleValue       = $this->getVisibleValue();
        $typeValue          = $this->getSlotTypeValue();
        $widthValue         = $this->getWidthValue($excelRow);
        $heightValue        = $this->getHeightValue($excelRow);
        $passbackMode       = $this->getPassBackModeValue($excelRow);
        $autoFitValue       = $this->getAutoFitValue($excelRow);

        $libraryAdSlot =[];
        $libraryAdSlot[self::AD_SLOT_NAME_KEY]      = $adSlotNameValue;
        $libraryAdSlot[self::VISIBLE_KEY]           = $visibleValue;
        $libraryAdSlot[self::TYPE_KEY]              = $typeValue;
        $libraryAdSlot[self::WIDTH_KEY]             = $widthValue;
        $libraryAdSlot[self::HEIGHT_KEY]            = $heightValue;
        $libraryAdSlot[self::PASS_BACK_MODE_KEY]    = $passbackMode;
        $libraryAdSlot[self::AUTO_FIT_KEY]          = $autoFitValue;
        $libraryAdSlot[self::PUBLISHER_KEY]         = $publisher;

        return $libraryAdSlot;
    }

    /**
     * @param $siteName
     * @param array $displayAdSlots
     * @return array
     * @throws \Exception
     */
    public function getAdSlotsDataBySiteName($siteName, array $displayAdSlots)
    {
        $expectAdSlots = [];
        foreach ($displayAdSlots as $displayAdSlot) {
            if(0 == strcmp($displayAdSlot[$this->getSiteNameIndex()], $siteName )) {
                $expectAdSlots[] = $displayAdSlot;
            }
        }
        return $expectAdSlots;
    }

    /**
     * @param $adSlotName
     * @param array $allAdSlots
     * @return mixed
     * @throws \Exception
     */
    public function getAdSlotDataByAdSlotName($adSlotName, array $allAdSlots)
    {
        foreach ($allAdSlots as $allAdSlot) {
            if(0 == strcmp($allAdSlot[$this->getAdSlotNameIndex()], $adSlotName )) {
                return $allAdSlot;
            }
        }
    }
    /**
     * @param $oneAdSlot
     * @return string
     * @throws \Exception
     */
    protected function getAdSlotNameValue($oneAdSlot)
    {
        if(array_key_exists(self::AD_SLOT_NAME_KEY, $this->adSlotsConfigs)) {
            return $oneAdSlot[$this->getAdSlotNameIndex()];
        }
        return self::AD_SLOT_NAME_DEFAULT_VALUE;
    }

    /**
     * @return bool
     */
    protected function getVisibleValue()
    {
        return self::VISIBLE_DEFAULT_VALUE;
    }

    /**
     * @param $oneAdSlot
     * @return null
     * @throws \Exception
     */
    protected function getWidthValue($oneAdSlot)
    {
        if (array_key_exists(self::WIDTH_KEY, $this->adSlotsConfigs)) {
            return $oneAdSlot[$this->getWidthIndex()];
        }

        return self::WIDTH_DEFAULT_VALUE;
    }

    /**
     * @param $oneAdSlot
     * @return null
     * @throws \Exception
     */
    protected function getHeightValue($oneAdSlot)
    {
        if (array_key_exists(self::HEIGHT_KEY, $this->adSlotsConfigs)) {
            return $oneAdSlot[$this->getHeightIndex()];
        }

        return self::HEIGHT_DEFAULT_VALUE;
    }

    /**
     * @param $oneAdSlot
     * @return bool
     * @throws \Exception
     */
    protected function getAutoFitValue($oneAdSlot)
    {
        if(array_key_exists(self::AUTO_FIT_KEY, $this->adSlotsConfigs)){
            return $oneAdSlot[$this->getAutoFitIndex()];
        }
        return self::AUTO_FIT_DEFAULT_VALUE;
    }

    /**
     * @param $oneAdSlot
     * @return string
     * @throws \Exception
     */
    protected function getPassBackModeValue($oneAdSlot)
    {
        if(array_key_exists(self::PASS_BACK_MODE_KEY, $this->adSlotsConfigs)) {
            return $oneAdSlot[$this->getPassBackModeIndex()];
        }
        return self::PASS_BACK_MODE_DEFAULT_VALUE;

    }

    /**
     * @param $oneAdSlot
     * @return int
     * @throws \Exception
     */
    protected function getRtbStatusValue($oneAdSlot)
    {
        if(array_key_exists(self::RTB_STATUS_KEY, $this->adSlotsConfigs)) {
            return $oneAdSlot[$this->getRtbStatusIndex()];
        }
        return self::RTB_DEFAULT_VALUE;

    }
    /**
     * @param $oneAdSlot
     * @return null
     * @throws \Exception
     */
    protected function getHbPriceValue($oneAdSlot)
    {
        if(array_key_exists(self::HEADER_BID_PRICE_KEY, $this->adSlotsConfigs)) {
            return $oneAdSlot[$this->getHeaderPriceIndex()];
        }
        return self::HEADER_BID_PRICE_DEFAULT_VALUE;
    }
    /**
     * @return string
     */
    protected function getSlotTypeValue()
    {
          return DisplayAdSlot::TYPE_DISPLAY;
    }

    protected function getFloorPriceValue ($oneAdSlot)
    {
        if(array_key_exists(self::FLOOR_PRICE_KEY, $this->adSlotsConfigs)) {
           return $oneAdSlot[$this->getFloorPriceIndex()];
        }
        return self::FLOOR_PRICE_DEFAULT_VALUE;

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getSiteNameIndex()
    {
        if (!array_key_exists(self::SITE_NAME_KEY, $this->adSlotsConfigs)){
            throw new \Exception(sprintf('There is not key =%s in configuration', self::SITE_NAME_KEY));
        }

        return $this->adSlotsConfigs[self::SITE_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAdSlotNameIndex()
    {
        if (!array_key_exists(self::AD_SLOT_NAME_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::AD_SLOT_NAME_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::AD_SLOT_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getWidthIndex()
    {
        if (!array_key_exists(self::WIDTH_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::WIDTH_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::WIDTH_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getHeightIndex()
    {
        if (!array_key_exists(self::HEIGHT_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::HEIGHT_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::HEIGHT_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getAutoFitIndex()
    {
        if (!array_key_exists(self::AUTO_FIT_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::AUTO_FIT_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::AUTO_FIT_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getHeaderPriceIndex()
    {
        if (!array_key_exists(self::HEADER_BID_PRICE_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::HEADER_BID_PRICE_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::HEADER_BID_PRICE_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getFloorPriceIndex()
    {
        if (!array_key_exists(self::FLOOR_PRICE_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::FLOOR_PRICE_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::FLOOR_PRICE_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getRtbStatusIndex()
    {
        if (!array_key_exists(self::RTB_STATUS_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::RTB_STATUS_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::RTB_STATUS_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getPassBackModeIndex()
    {
        if (!array_key_exists(self::PASS_BACK_MODE_KEY, $this->adSlotsConfigs)) {
            throw new \Exception(sprintf('There is not key =%s in config string %s', self::PASS_BACK_MODE_KEY, $this->adSlotsConfigs));
        }

        return $this->adSlotsConfigs[self::PASS_BACK_MODE_KEY];
    }
} 