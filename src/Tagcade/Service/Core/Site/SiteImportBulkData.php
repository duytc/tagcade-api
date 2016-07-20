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
use Tagcade\Service\Core\AdTag\AdTagImportBulkDataInterface;

class SiteImportBulkData implements SiteImportBulkDataInterface
{

    use CreateSiteTokenTrait;

    const NAME_KEY                              = 'name';
    const DOMAIN_KEY                            = 'domain';
    const SOURCE_REPORT_KEY                     = 'enableSourceReport';
    const RTB_STATUS_KEY                        = 'rtbStatus';
    const PLAYER_KEY                            = 'players';
    const PUBLISHER_KEY                         = 'publisher';
    const SITE_NAME_KEY                         = 'site';

    const ENABLE_SOURCE_REPORT_DEFAULT_VALUE    = false;
    const RTB_STATUS_DEFAULT_VALUE              = 2;
    const PLAYER_DEFAULT_VALUE                  = null;

    const SITE_SHEET_NAME                       =   'Sites';
    const AD_TAGS_SHEET_NAME                    =   'Ad Tags';
    const DISPLAY_AD_SLOT_NAME                  =   'Display Ad Slots';
    const DYNAMIC_AD_SLOT_NAME                  =   'Dynamic Ad Slots';
    const EXPRESSION_TARGETING_NAME             =   'Expression Targeting';
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
    /**
     * @var AdTagImportBulkDataInterface
     */
    private $adTagImportBulkData;

    function __construct(SiteManagerInterface $siteManager, DisplayAdSlotImportBulkDataInterface $displayAdSlotBulkUpload,
                         DynamicAdSlotImportBulkDataInterface $dynamicAdSlotBulkUpload, AdTagImportBulkDataInterface $adTagImportBulkData ,
                         $siteConfigs, Logger $logger)
    {
        $this->siteManager = $siteManager;
        $this->siteConfigs = $siteConfigs;
        $this->logger = $logger;
        $this->displayAdSlotBulkUpload = $displayAdSlotBulkUpload;
        $this->dynamicAdSlotBulkUpload = $dynamicAdSlotBulkUpload;
        $this->adTagImportBulkData = $adTagImportBulkData;
    }

    /**
     * @param array $arrayMapSitesData
     * @param PublisherInterface $publisher
     * @param array $dryOption
     * @return array|mixed
     */

    public function createSites(array $arrayMapSitesData, PublisherInterface $publisher, $dryOption)
    {
        $siteObjects = [];
        foreach ($arrayMapSitesData as $site) {
            $displayAdSlotsOfThisSite = [];
            $dynamicAdSlotsOfThisSite = [];
            if (array_key_exists('displayAdSlots',$site)) {
                $displayAdSlotsOfThisSite = $this->getDisplayAdSlotForSite($site);
                unset($site['displayAdSlots']);
            }
            if (array_key_exists('dynamicAdSlots', $site)) {
                $dynamicAdSlotsOfThisSite = $this->getDynamicAdSlotForSite($site);
                unset($site['dynamicAdSlots']);
            }

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

            if (count($displayAdSlotsOfThisSite) > 0) {
                $displayAdSlots = $this->displayAdSlotBulkUpload->importDisplayAdSlots($displayAdSlotsOfThisSite, $siteObject ,$publisher, $dryOption);
                $adSlots = $displayAdSlots;
            }

            if ((count($dynamicAdSlotsOfThisSite) > 0) && (count($adSlots)) >0) {
                $dynamicAdSlotsObject = $this->dynamicAdSlotBulkUpload->importDynamicAdSlots($dynamicAdSlotsOfThisSite, $siteObject, $adSlots, $dryOption);
                $adSlots = array_merge($adSlots, $dynamicAdSlotsObject);
            }
            $siteObject->setAdSlots($adSlots);

            if (false == $dryOption) {
                $this->siteManager->persists($siteObject);
                $this->siteManager->flush();
            }

            $siteObjects[] = $siteObject;
        }

        $numSite = count($siteObjects);
        if (true == $dryOption) {
            $this->logger->info(sprintf('Total Site import: %d', $numSite));
        }

        return $siteObjects;
    }

    /**
     * @param $site
     * @return mixed
     */
    protected function getDisplayAdSlotForSite($site)
    {
        $displayAdSlots =  $site['displayAdSlots'];

        return $displayAdSlots;
    }

    /**
     * @param $siteName
     * @param array $displayAdSlots
     * @param array $adTags
     * @return array
     */
    protected function getDisplayAdSlotsForSiteByName($siteName, array $displayAdSlots, array $adTags)
    {
        $expectAdSlots = [];
        $siteNameIndexInDisplayAdSlotSheet = $this->displayAdSlotBulkUpload->getSiteNameIndex();
        $adSlotNameIndex = $this->displayAdSlotBulkUpload->getAdSlotNameIndex();
        foreach ($displayAdSlots as $displayAdSlot) {
            if( 0 == strcmp($siteName, $displayAdSlot[$siteNameIndexInDisplayAdSlotSheet])) {
                $displayAdSlotName = $displayAdSlot[$adSlotNameIndex];
                $expectAdSlots[$displayAdSlotName] = $displayAdSlot;
            }
        }

        $adSlotNameIndex = $this->displayAdSlotBulkUpload->getAdSlotNameIndex();
        $expectAdSlotsWithAdTags = [];
        foreach ($expectAdSlots as $expectAdSlot) {
          $displayAdSlotName = $expectAdSlot[$adSlotNameIndex];
            $adTagsOfOneAdSlot = [];
            foreach ($adTags as $adTag) {
                $adSlotNameIndexOfAdTag = $this->adTagImportBulkData->getAdSlotNameIndex();
                $adSlotNameInAdTag = $adTag[$adSlotNameIndexOfAdTag];
                if( 0 == strcmp($displayAdSlotName, $adSlotNameInAdTag)) {
                    $adTagsOfOneAdSlot[] = $adTag;
                }
            }
            $expectAdSlot['adTags'] = $adTagsOfOneAdSlot;
            $expectAdSlotsWithAdTags [$displayAdSlotName] = $expectAdSlot;
        }

        return $expectAdSlotsWithAdTags;
    }

    /**
     * @param $siteName
     * @param array $dynamicAdSlots
     * @param array $targetingExpression
     * @return array
     */
    protected function getDynamicAdSlotsForSiteByName($siteName, array $dynamicAdSlots, array $targetingExpression)
    {
        $expectAdSlots = [];
        $siteNameIndexInDynamicAdSlotSheet = $this->dynamicAdSlotBulkUpload->getSiteNameIndexOfDynamicAdSlot();
        $dynamicAdSlotNameIndex = $this->dynamicAdSlotBulkUpload->getNameIndexOfDynamicAdSlot();
        $targetingExpressionArray = $this->dynamicAdSlotBulkUpload->convertExpressionTargetingToArray($targetingExpression);
        foreach ($dynamicAdSlots as $dynamicAdSlot) {
            if( 0 == strcmp($siteName, $dynamicAdSlot[$siteNameIndexInDynamicAdSlotSheet])) {
                $dynamicAdSlotName                      = $dynamicAdSlot[$dynamicAdSlotNameIndex];
                $dynamicBuilderExpression               = $targetingExpressionArray[$dynamicAdSlotName];
                $dynamicAdSlot['builderExpressions']    = $dynamicBuilderExpression;
                $expectAdSlots[$dynamicAdSlotName]      = $dynamicAdSlot;
            }
        }

        return $expectAdSlots;
    }

    /**
     * @param $site
     * @return mixed
     */
    protected function getDynamicAdSlotForSite($site)
    {
        $dynamicAdSlots =  $site['dynamicAdSlots'];
        unset($site['dynamicAdSlots']);

        return $dynamicAdSlots;
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

    public function createFullDataForSites($excelFileArray, PublisherInterface $publisher)
    {
        $excelSites                  = $this->getSitesFromExcelArray($excelFileArray);
        $excelDisplayAdSlots         = $this->getDisplayAdSlotsFromExcelArray($excelFileArray);
        $excelAdTags                 = $this->getAdTagsFromExcelArray($excelFileArray);
        $excelDynamicAdSlots         = $this->getDynamicAdSlotsFromExcelArray($excelFileArray);
        $excelExpressionsTargeting   = $this->getExpressionTargetingFromExcelArray($excelFileArray);

        $allSites = [];
        foreach ($excelSites as $inputSite) {
            $siteName               = $this->getNameSiteValue($inputSite);
            $domain                 = $this->getDomainValue($inputSite);
            $sourceReportValue      = $this->getEnableSourceReportValue($inputSite);
            $rtbStatusValue         = $this->getRtbStatusValue($inputSite);
            $playerValue            = $this->getPlayerValue($inputSite);

            $oneSite[self::PUBLISHER_KEY]       = $publisher;
            $oneSite[self::NAME_KEY]            = $siteName;
            $oneSite[self::DOMAIN_KEY]          = $domain;
            $oneSite[self::SOURCE_REPORT_KEY]   = $sourceReportValue;
            $oneSite[self::RTB_STATUS_KEY]      = $rtbStatusValue;
            $oneSite[self::PLAYER_KEY]          = $playerValue;

            if (null != $excelDisplayAdSlots) {
                $displayAdSlots = $this->getDisplayAdSlotsForSiteByName($siteName, $excelDisplayAdSlots,$excelAdTags);
                $oneSite['displayAdSlots']   = $displayAdSlots;
            }
            if (null != $excelDynamicAdSlots && null != $excelExpressionsTargeting) {
                $dynamicAdSlots = $this->getDynamicAdSlotsForSiteByName($siteName, $excelDynamicAdSlots, $excelExpressionsTargeting);
                $oneSite['dynamicAdSlots']   = $dynamicAdSlots;
            }
            $allSites[$siteName] = $oneSite;
        }

        return $allSites;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getSitesFromExcelArray($contents)
    {
        if (array_key_exists(self::SITE_SHEET_NAME, $contents)){
            $sites = $contents[self::SITE_SHEET_NAME];
            array_shift($sites); // Remove header of site sheet
            return $sites;
        }
        return null;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getDisplayAdSlotsFromExcelArray($contents)
    {
        if (array_key_exists(self::DISPLAY_AD_SLOT_NAME,$contents)) {
            $displayAdSlotData = $contents[self::DISPLAY_AD_SLOT_NAME];
            array_shift($displayAdSlotData); // Remove header of display ad slot
            return $displayAdSlotData;
        }
        return null;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getAdTagsFromExcelArray($contents)
    {
        if (array_key_exists(self::AD_TAGS_SHEET_NAME,$contents)) {
            $adTagsData = $contents[self::AD_TAGS_SHEET_NAME];
            array_shift($adTagsData); // Remove header of display ad slot
            return $adTagsData;
        }
        return null;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getDynamicAdSlotsFromExcelArray($contents)
    {
        if (array_key_exists(self::DYNAMIC_AD_SLOT_NAME, $contents)) {
            $dynamicAdSlotData = $contents[self::DYNAMIC_AD_SLOT_NAME];
            array_shift($dynamicAdSlotData); // Remove header of display ad slot
            return $dynamicAdSlotData;
        }
        return null;

    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getExpressionTargetingFromExcelArray($contents)
    {
        if (array_key_exists(self::EXPRESSION_TARGETING_NAME,$contents)) {
            $expressionTargeting = $contents[self::EXPRESSION_TARGETING_NAME];
            array_shift($expressionTargeting); // Remove header of expression targeting
            return $expressionTargeting;
        }
        return null;

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