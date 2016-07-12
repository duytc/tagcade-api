<?php


namespace Tagcade\Service\Core\Site;


use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Tagcade\Behaviors\CreateSiteTokenTrait;
use Tagcade\DomainManager\SiteManagerInterface;

use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Core\AdSlot\DisplayAdSlotImportBulkDataInterface;
use Tagcade\Service\Core\AdSlot\DynamicAdSlotImportBulkDataInterface;

class SiteImportBulkData implements SiteImportBulkDataInterface
{

    use CreateSiteTokenTrait;

    const NAME_KEY = 'name';
    const DOMAIN_KEY = 'domain';
    const SOURCE_REPORT_KEY = 'enableSourceReport';
    const RTB_STATUS_KEY = 'rtbStatus';
    const PLAYER_KEY = 'players';
    const PUBLISHER_KEY = 'publisher';

    const ENABLE_SOURCE_REPORT_DEFAULT_VALUE = false;
    const RTB_STATUS_DEFAULT_VALUE = 2;
    const PLAYER_DEFAULT_VALUE = null;

    const SITE_NAME_KEY                = 'site';

    /**
     * @var SiteManagerInterface
     */
    private $siteManager;
    /**
     * @var
     */
    private $siteConfigs;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var DisplayAdSlotImportBulkDataInterface
     */
    private $displayAdSlotBulkUpload;
    /**
     * @var DynamicAdSlotImportBulkDataInterface
     */
    private $dynamicAdSlotBulkUpload;

    function __construct(SiteManagerInterface $siteManager, DisplayAdSlotImportBulkDataInterface $displayAdSlotBulkUpload, DynamicAdSlotImportBulkDataInterface $dynamicAdSlotBulkUpload , $siteConfigs, Logger $logger)
    {
        $this->siteManager = $siteManager;
        $this->siteConfigs = $siteConfigs;
        $this->logger = $logger;
        $this->displayAdSlotBulkUpload = $displayAdSlotBulkUpload;
        $this->dynamicAdSlotBulkUpload = $dynamicAdSlotBulkUpload;
    }


    /**
     * @param array $arrayMapSitesData
     * @param PublisherInterface $publisher
     * @param array $arrayMapDisplayAdSlot
     * @param array $dynamicAdSlots
     * @param array $expression
     * @param array $arrayMapAdTags
     * @param $dryOption
     * @return array|mixed
     */
    public function createSites(array $arrayMapSitesData, PublisherInterface $publisher, array $arrayMapDisplayAdSlot,
                                array $dynamicAdSlots, array $expression , array $arrayMapAdTags, $dryOption)
    {
        $siteObjects = [];
        foreach ($arrayMapSitesData as $site) {

            $siteObject = Site::createSiteFromArray($site);
            $siteToken = $this->createSiteHash ($siteObject->getPublisherId(), $siteObject->getDomain());

            $existSites = $this->siteManager->getSiteBySiteToken($siteToken);
            if (count($existSites) >0) {
                $siteObject = array_shift($existSites);
                $this->logger->warning(sprintf('This site %s of publisher = %d has existed in system!', $siteObject->getDomain(), $publisher->getId()));
            }

            if (true == $dryOption) {
                $this->logger->info(sprintf('Import Site: %s', $siteObject->getName()));
            }

            $adSlots =[];
            $displayAdSlotsOfThisSite = $this->getDisplayAdSlotForSite($siteObject, $arrayMapDisplayAdSlot);
            if (count($displayAdSlotsOfThisSite) > 0) {
                $displayAdSlots = $this->displayAdSlotBulkUpload->importDisplayAdSlots($displayAdSlotsOfThisSite, $arrayMapAdTags,$dryOption);
                $adSlots = $displayAdSlots;
            }

            $dynamicAdSlotsOfThisSite = $this->getDynamicAdSlotForSite($siteObject, $dynamicAdSlots);
            if ((count($dynamicAdSlotsOfThisSite) > 0) && (count($adSlots)) >0) {
                $dynamicAdSlotsObject = $this->dynamicAdSlotBulkUpload->importDynamicAdSlots($dynamicAdSlots, $expression, $siteObject, $adSlots, $dryOption);
                $adSlots = array_merge($adSlots, $dynamicAdSlotsObject);
            }
            $siteObject->setAdSlots($adSlots);

            if (false == $dryOption) {
                $this->siteManager->persists($siteObject);
            }

            $siteObjects[] = $siteObject;
        }

        $numSite = count($siteObjects);
        if (false == $dryOption) {
            $this->siteManager->flush();
        } else {
            $this->logger->info(sprintf('Total Site import: %d', $numSite));
        }

        return $siteObjects;
    }

    /**
     * @param Site $site
     * @param array $displayAdSlots
     * @return array
     */
    protected function getDisplayAdSlotForSite(Site $site, array $displayAdSlots)
    {
        $expectAdSlots = [];
        foreach ($displayAdSlots as $displayAdSlot) {
            if( 0 == strcmp($site->getName(),$displayAdSlot[self::SITE_NAME_KEY])) {
                $displayAdSlot[self::SITE_NAME_KEY]=$site;
                $expectAdSlots[] = $displayAdSlot;
            }
        }

        return $expectAdSlots;
    }

    /**
     * @param Site $siteObject
     * @param $dynamicAdSlots
     * @return array
     */
    protected function getDynamicAdSlotForSite(Site $siteObject, $dynamicAdSlots)
    {
        $expectDynamicAdSlots = [];
        $indexOfSiteName = $this->dynamicAdSlotBulkUpload->getSiteNameIndexOfDynamicAdSlot();
        foreach ($dynamicAdSlots as $dynamicAdSlot) {
            if( 0 == strcmp($siteObject->getName(),$dynamicAdSlot[$indexOfSiteName])) {
                $expectDynamicAdSlots[] = $dynamicAdSlot;
            }
        }
        return $expectDynamicAdSlots;
    }


    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return array
     */
    public function createDataForSites($excelRows, PublisherInterface $publisher)
    {
        $sitesData= [];
        foreach ($excelRows as $excelRow) {
           $site =  $this->createDataForOneSite($excelRow,$publisher);
            $sitesData[] = $site;
        }

        return $sitesData;

    }
    /**
     * @param $inputSite
     * @param PublisherInterface $publisher
     * @return array|mixed
     */
    public function createDataForOneSite($inputSite, PublisherInterface $publisher)
    {
        $oneSite = [];

        $siteName = $this->getNameSiteValue($inputSite);
        $domain = $this->getDomainValue($inputSite);
        $sourceReportValue = $this->getEnableSourceReportValue($inputSite);
        $rtbStatusValue = $this->getRtbStatusValue($inputSite);
        $playerValue = $this->getPlayerValue($inputSite);

        $oneSite[self::PUBLISHER_KEY]       = $publisher;
        $oneSite[self::NAME_KEY]            = $siteName;
        $oneSite[self::DOMAIN_KEY]          = $domain;
        $oneSite[self::SOURCE_REPORT_KEY]   = $sourceReportValue;
        $oneSite[self::RTB_STATUS_KEY]      = $rtbStatusValue;
        $oneSite[self::PLAYER_KEY]          = $playerValue;

        return $oneSite;
    }

    /**
     * @param $oneSite
     * @return mixed
     * @throws \Exception
     */
    protected function getNameSiteValue($oneSite)
    {
        return $oneSite[$this->getNameIndex()];
    }

    /**
     * @param $oneSite
     * @return mixed
     * @throws \Exception
     */
    protected function getDomainValue($oneSite)
    {
        return $oneSite[$this->getDomainIndex()];
    }

    /**
     * @param $oneSite
     * @return bool
     * @throws \Exception
     */
    protected function getEnableSourceReportValue($oneSite)
    {
        if(array_key_exists(self::SOURCE_REPORT_KEY, $this->siteConfigs) && array_key_exists($this->getSourceReportIndex(), $oneSite)){
            return    $oneSite[$this->getSourceReportIndex()];
        }

        return self::ENABLE_SOURCE_REPORT_DEFAULT_VALUE;
    }

    /**
     * @param $oneSite
     * @return int
     * @throws \Exception
     */
    protected function getRtbStatusValue($oneSite)
    {
        if(array_key_exists(self::RTB_STATUS_KEY, $this->siteConfigs) && array_key_exists($this->getRtbStatusIndex(), $oneSite)){
            return    $oneSite[$this->getRtbStatusIndex()];
        }

        return (self::RTB_STATUS_DEFAULT_VALUE);
    }

    /**
     * @param $oneSite
     * @return null
     * @throws \Exception
     */
    protected function getPlayerValue($oneSite)
    {
        if(array_key_exists(self::PLAYER_KEY, $this->siteConfigs) && array_key_exists($this->getPlayerIndex(), $oneSite)){
            return    $oneSite[$this->getRtbStatusIndex()];
        }

        return (self::PLAYER_DEFAULT_VALUE);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getNameIndex()
    {
        if(!array_key_exists('name', $this->siteConfigs)){
            throw new \Exception('There is not site name in config file');
        }
        return $this->siteConfigs['name'];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getDomainIndex()
    {

        if(!array_key_exists(self::DOMAIN_KEY, $this->siteConfigs)) {
            throw new \Exception ('There is not domain name in config file');
        }

        return $this->siteConfigs[self::DOMAIN_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getSourceReportIndex()
    {
        if(!array_key_exists(self::SOURCE_REPORT_KEY, $this->siteConfigs)) {
            throw new \Exception ('There is not source report in config file');
        }

        return $this->siteConfigs[self::SOURCE_REPORT_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getRtbStatusIndex()
    {
        if(!array_key_exists(self::RTB_STATUS_KEY, $this->siteConfigs)) {
            throw new \Exception ('There is not source report in config file');
        }

        return $this->siteConfigs[self::RTB_STATUS_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getPlayerIndex()
    {
        if(!array_key_exists(self::PLAYER_KEY, $this->siteConfigs)) {
            throw new \Exception ('There is not player in config file');
        }

        return $this->siteConfigs[self::PLAYER_KEY];
    }
} 