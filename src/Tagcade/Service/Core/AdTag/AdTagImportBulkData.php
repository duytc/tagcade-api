<?php


namespace Tagcade\Service\Core\AdTag;


use Monolog\Logger;
use Symfony\Component\Security\Acl\Exception\Exception;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Model\Core\AdNetworkInterface;

use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagImportBulkData implements AdTagImportBulkDataInterface
{

    const SITE_SHEET_NAME               =   'Sites';
    const AD_TAGS_SHEET_NAME            =   'Ad Tags';
    const DISPLAY_AD_SLOT_SHEET_NAME    =   'Display Ad Slots';
    const DYNAMIC_AD_SLOT_NAME          =   'Dynamic Ad Slots';
    const EXPRESSION_TARGETING_NAME     =   'Expression Targeting';

    const POSITION_KEY_OF_AD_TAG                      = 'position';
    const ACTIVE_KEY_OF_AD_TAG                        = 'active';
    const FREQUENCY_CAP_KEY_OF_AD_TAG                 ='frequencyCap';
    const ROTATION_KEY_OF_AD_TAG                      = 'rotation';
    const IMPRESSION_CAP_KEY_OF_AD_TAG                = 'impressionCap';
    const NETWORK_OPPORTUNITY_CAP_KEY_OF_AD_TAG       = 'networkOpportunityCap';
    const LIBRARY_AD_TAG_KEY_OF_AD_TAG                = 'libraryAdTag';
    const AD_SLOT_KEY_OF_AD_TAG                       = 'adSlot';
    const REF_ID_KEY_OF_AD_TAG                        = 'refId';


    const NAME_KEY_OF_LIB_AD_TAG                       = 'name';
    const HTML_KEY_OF_LIB_AD_TAG                       = 'html';
    const VISIBLE_KEY_OF_LIB_AD_TAG                    = 'visible';
    const AD_TYPE_KEY_OF_LIB_AD_TAG                    = 'adType';
    const AD_NETWORK_KEY_OF_LIB_AD_TAG                 = 'adNetwork';
    const PARTNER_TAG_ID_KEY_OF_AD_TAG                 = 'partnerTagId';


    const HTML_DEFAULT_VALUE_OF_LIB_AD_TAG              = null;
    const VISIBLE_DEFAULT_VALUE_OF_LIB_AD_TAG           = false;
    const AD_TYPE_DEFAULT_VALUE_OF_LIB_AD_TAG           = 0;
    const PARTNER_TAG_ID_DEFAULT_VALUE_OF_AD_TAG        = null;


    const AD_SLOT_NAME_KEY                             ='adSlotName';

    const POSITION_DEFAULT_VALUE                        = 1;
    const ACTIVE_DEFAULT_VALUE                          = 1;
    const FREQUENCY_CAP_DEFAULT_VALUE                   = null;
    const ROTATION_DEFAULT_VALUE                        = null;
    const IMPRESSION_CAP_DEFAULT_VALUE                  = null;
    const NETWORK_OPPORTUNITY_CAP_DEFAULT_VALUE         = null;


    /**
     * @var $adTagConfigs
     */
    private $adTagConfigs;
    /**
     * @var AdNetworkManagerInterface
     */
    private $adNetworkManager;
    /**
     * @var Logger
     */
    private $logger;

    function __construct( AdNetworkManagerInterface $adNetworkManager, Logger $logger, $adTagConfigs)
    {
        $this->adTagConfigs = $adTagConfigs;
        $this->adNetworkManager = $adNetworkManager;
        $this->logger = $logger;
    }

    /**
     * @param DisplayAdSlot $displayAdSlotObject
     * @param array $allAdTags
     * @param $dryOption
     * @return array|mixed
     */

    public function importAdTagsForOneAdSlot(DisplayAdSlot $displayAdSlotObject, array $allAdTags, $dryOption)
    {
        $adTagObjects = [];

        foreach($allAdTags as $AdTag) {
            $AdTag[self::AD_SLOT_KEY_OF_AD_TAG] = $displayAdSlotObject;
            $adTagObject = AdTag::createAdTagFromArray($AdTag);
            $adTagObjects [] = $adTagObject;
        }

        if(true == $dryOption) {
            $this->logger->info(sprintf('       Total %d ad tags import to display ad slot: %s', count($adTagObjects), $displayAdSlotObject->getName()));
        }

        return $adTagObjects;
    }

    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return array
     */
    public function createAllAdTagsData($excelRows, PublisherInterface $publisher)
    {
        $allAdTagsData = [];

        foreach ($excelRows as $excelRow){
            $adTagData = $this->createAdTagDataFromExcelRow($excelRow, $publisher);
            $allAdTagsData [] = $adTagData;
        }

        return $allAdTagsData;
    }

    /**
     * @param $excelRow
     * @param PublisherInterface $publisher
     * @return array
     * @throws \Exception
     */
    protected function createAdTagDataFromExcelRow($excelRow, PublisherInterface $publisher)
    {
        $adTag = [];

        $positionValue = $this->getPositionValue($excelRow);
        $activeValue = $this->getActiveValue($excelRow);
        $rotationValue = $this->getRotationValue($excelRow);
        $frequencyCapValue = $this->getFrequencyCapValue($excelRow);
        $impressionCapValue = $this->getImpressionCapValue($excelRow);
        $networkOpportunityCapValue = $this->getNetworkOpportunityCapValue($excelRow);

        $libraryAdTagData = $this->createLibraryAdTagDataFromExcelRow($excelRow, $publisher);
        $libraryAdTagObject = LibraryAdTag::createAdTagLibraryFromArray($libraryAdTagData);

        $adTag[self::POSITION_KEY_OF_AD_TAG] = $positionValue;
        $adTag[self::ACTIVE_KEY_OF_AD_TAG] = $activeValue;
        $adTag[self::ROTATION_KEY_OF_AD_TAG] = $rotationValue;
        $adTag[self::FREQUENCY_CAP_KEY_OF_AD_TAG] = $frequencyCapValue;
        $adTag[self::IMPRESSION_CAP_KEY_OF_AD_TAG] = $impressionCapValue;
        $adTag[self::NETWORK_OPPORTUNITY_CAP_KEY_OF_AD_TAG] = $networkOpportunityCapValue;
        $adTag[self::LIBRARY_AD_TAG_KEY_OF_AD_TAG] = $libraryAdTagObject;
        $adTag[self::AD_SLOT_KEY_OF_AD_TAG] = $excelRow[$this->getAdSlotNameIndex()];
        $adTag[self::REF_ID_KEY_OF_AD_TAG] = $this->getRefId();

        return $adTag;
    }

    /**
     * @param $excelRow
     * @param PublisherInterface $publisher
     * @return array
     * @throws \Exception
     */
    protected function createLibraryAdTagDataFromExcelRow($excelRow, PublisherInterface $publisher)
    {
        $libraryAdTag = [];

        $demandPartnerName = $excelRow[$this->getDemandPartnerIndex()];
        if(empty($demandPartnerName)) {
            throw new \Exception('Demand Partner can not be empty!');
        }

        $adNetworkValue = $this->getAdNetWorkByDemandPartNameForPublisher($demandPartnerName, $publisher);
        if (null == $adNetworkValue) {
            throw new \Exception(sprintf('Publisher %d has not demand partner name: %s', $publisher->getId(), $demandPartnerName));
        }

        $adTagNameValue = $this->getNameValueForAdTagLibrary($excelRow);
        $htmlValue = $this->getHtmlValue($excelRow);
        $visibleValue = $this->getVisibleValueForAdTagLibrary($excelRow);
        $adTypeValue = $this->getAdTypeValueForAdTagLibrary($excelRow);
        $partnerTagIdValue = $this->getPartnerTagIdForAdTagLibrary($excelRow);

        $libraryAdTag[self::NAME_KEY_OF_LIB_AD_TAG] = $adTagNameValue;
        $libraryAdTag[self::AD_NETWORK_KEY_OF_LIB_AD_TAG] = $adNetworkValue;
        $libraryAdTag[self::HTML_KEY_OF_LIB_AD_TAG] = $htmlValue;
        $libraryAdTag[self::VISIBLE_KEY_OF_LIB_AD_TAG] = $visibleValue;
        $libraryAdTag[self::AD_TYPE_KEY_OF_LIB_AD_TAG] = $adTypeValue;
        $libraryAdTag[self::PARTNER_TAG_ID_KEY_OF_AD_TAG] = $partnerTagIdValue;

        return $libraryAdTag;
    }

    /**
     * @param $demandPartName
     * @param PublisherInterface $publisher
     * @return AdNetworkInterface
     * @throws \Exception
     */
    protected function getAdNetWorkByDemandPartNameForPublisher($demandPartName, PublisherInterface $publisher)
    {
        /**
         * @var AdNetworkInterface[] $adNetworks
         */
        $adNetworks = $this->adNetworkManager->getAdNetworksForPublisher($publisher);

        if( null == $adNetworks) {
            throw new \Exception('Not found demand partner in system!');
        }

        foreach ($adNetworks as $adNetwork) {
            if ( 0 == strcmp($demandPartName, $adNetwork->getName())){
             return $adNetwork;
            }
        }
    }

    /**
     * @param $adSlotName
     * @param array $adTags
     * @return array
     * @throws \Exception
     */
    public function getAllAdTagsForOneAdSlot($adSlotName, array $adTags)
    {
        $expectAdTags = [];
        foreach ($adTags as $adTag) {
            if(0 == strcmp($adTag[$this->getAdSlotNameIndex()], $adSlotName )) {
                $expectAdTags[] = $adTag;
            }
        }

        return $expectAdTags;
    }

    /**
     * @return string
     */
    protected function getRefId()
    {
        return uniqid('', true);
    }

    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getPartnerTagIdForAdTagLibrary ($rawAdTag)
    {
        if (array_key_exists(self::PARTNER_TAG_ID_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getPartnerTagIdIndex(), $rawAdTag)) {
           return $rawAdTag[$this->getPartnerTagIdIndex()];
        }

        return self::PARTNER_TAG_ID_DEFAULT_VALUE_OF_AD_TAG;
    }

    /**
     * @param $rawAdTag
     * @return int
     * @throws \Exception
     */
    protected function getAdTypeValueForAdTagLibrary($rawAdTag)
    {
        if (array_key_exists(self::AD_TYPE_KEY_OF_LIB_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getAdTypeIndex(), $rawAdTag)) {
            return $rawAdTag[$this->getAdTypeIndex()];
        }

        return self::AD_TYPE_DEFAULT_VALUE_OF_LIB_AD_TAG;
    }

    /**
     * @param $rawAdTag
     * @return bool
     * @throws \Exception
     */
    protected function getVisibleValueForAdTagLibrary($rawAdTag)
    {
        if (array_key_exists(self::VISIBLE_KEY_OF_LIB_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getVisibleIndex(), $rawAdTag)) {
            return $rawAdTag[$this->getVisibleIndex()];
        }

        return self::VISIBLE_DEFAULT_VALUE_OF_LIB_AD_TAG;
    }

    /**
     * @param $rawAdTag
     * @return mixed
     * @throws \Exception
     */
    protected function getNameValueForAdTagLibrary($rawAdTag)
    {
        if (array_key_exists(self::NAME_KEY_OF_LIB_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getAdSlotNameIndex(), $rawAdTag)) {
            return $rawAdTag[$this->getAdSlotNameIndex()];
        }

        return $rawAdTag[$this->getDemandPartnerIndex()]; // Default name of ad tag is demand partner name
    }

    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getNetworkOpportunityCapValue($rawAdTag)
    {
        if (array_key_exists(self::NETWORK_OPPORTUNITY_CAP_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getOpportunityCapIndex(), $rawAdTag)) {
          return $rawAdTag[$this->getOpportunityCapIndex()];
        }

        return self:: NETWORK_OPPORTUNITY_CAP_DEFAULT_VALUE;
    }

    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getImpressionCapValue($rawAdTag)
    {
        if (array_key_exists(self::IMPRESSION_CAP_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getImpressionCapIndex(), $rawAdTag)) {
            return $rawAdTag[$this->getImpressionCapIndex()];
        }

        return self:: IMPRESSION_CAP_DEFAULT_VALUE;
    }
    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getRotationValue($rawAdTag)
    {
        if (array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getRotationIndex(),$rawAdTag)) {
                return $rawAdTag[$this->getRotationIndex()];
        }

        return self:: ROTATION_DEFAULT_VALUE;
    }
    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getFrequencyCapValue($rawAdTag)
    {
        if (array_key_exists(self::FREQUENCY_CAP_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getFrequencyCapIndex(),$rawAdTag)) {
            return $rawAdTag[$this->getFrequencyCapIndex()];
        }

        return self:: FREQUENCY_CAP_DEFAULT_VALUE;

    }

    /**
     * @param $rawAdTag
     * @return int
     * @throws \Exception
     */
    protected function getPositionValue($rawAdTag)
    {
        if (array_key_exists(self::POSITION_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getPositionIndex(),$rawAdTag)) {
            return $rawAdTag[$this->getPositionIndex()];
        }

        return self:: POSITION_DEFAULT_VALUE;
    }

    /**
     * @param $rawAdTag
     * @return int
     * @throws \Exception
     */
    protected function getActiveValue($rawAdTag)
    {
        if (array_key_exists(self::ACTIVE_KEY_OF_AD_TAG, $this->adTagConfigs)  && array_key_exists($this->getActiveIndex(),$rawAdTag)) {
            return $rawAdTag[$this->getActiveIndex()];
        }
        return self:: ACTIVE_DEFAULT_VALUE;

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAdSlotNameIndex()
    {
        if(!array_key_exists(self::AD_SLOT_NAME_KEY, $this->adTagConfigs)) {
            throw new \Exception ('There is not ad slot in config file');
        }

        return $this->adTagConfigs[self::AD_SLOT_NAME_KEY];
    }


    public function getAdTagNameIndex()
    {
        if(!array_key_exists(self::NAME_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)){
            throw new \Exception ('There is not ad tag name in config file');
        }

        return $this->adTagConfigs[self::AD_SLOT_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getDemandPartnerIndex()
    {
        if(!array_key_exists(self::AD_NETWORK_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not demand partner in config file');
        }

        return $this->adTagConfigs[self::AD_NETWORK_KEY_OF_LIB_AD_TAG];

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getTagHtmlIndex()
    {
        if(!array_key_exists(self::HTML_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not tag html in config file');
        }

        return $this->adTagConfigs[self::HTML_KEY_OF_LIB_AD_TAG];
    }

    protected function getHtmlValue($rawAdTag)
    {
        if(!array_key_exists(self::HTML_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not tag html in config file');
        }

        return $rawAdTag[$this->getTagHtmlIndex()];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getPositionIndex()
    {
        if(!array_key_exists(self::POSITION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not position in config file');
        }

        return $this->adTagConfigs[self::POSITION_KEY_OF_AD_TAG];

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getWeightIndex()
    {
        if(!array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not weight key in config file');
        }

        return $this->adTagConfigs[self::ROTATION_KEY_OF_AD_TAG];

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getActiveIndex()
    {
        if(!array_key_exists(self::ACTIVE_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not active key in config file');
        }

        return $this->adTagConfigs[self::ACTIVE_KEY_OF_AD_TAG];

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getRotationIndex()
    {
        if(!array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not rotation key in config file');
        }

        return $this->adTagConfigs[self::ROTATION_KEY_OF_AD_TAG];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getImpressionCapIndex()
    {
        if (!array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not impression key in config file');
        }

        return $this->adTagConfigs[self::ROTATION_KEY_OF_AD_TAG];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getOpportunityCapIndex()
    {
        if (!array_key_exists(self::NETWORK_OPPORTUNITY_CAP_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not Opportunity cap key in config file');
        }

        return $this->adTagConfigs[self::NETWORK_OPPORTUNITY_CAP_KEY_OF_AD_TAG];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getVisibleIndex()
    {
        if (!array_key_exists(self::VISIBLE_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not visible key in config file');
        }

        return $this->adTagConfigs[self::VISIBLE_KEY_OF_LIB_AD_TAG];

    }


    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getAdTypeIndex()
    {
        if (!array_key_exists(self::AD_TYPE_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not ad type key in config file');
        }

        return $this->adTagConfigs[self::AD_TYPE_KEY_OF_LIB_AD_TAG];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function  getPartnerTagIdIndex()
    {
        if (!array_key_exists(self::PARTNER_TAG_ID_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not partner tag id key in config file');
        }

        return $this->adTagConfigs[self::PARTNER_TAG_ID_KEY_OF_AD_TAG];

    }


    protected function  getFrequencyCapIndex()
    {
        if (!array_key_exists(self::FREQUENCY_CAP_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not partner tag id key in config file');
        }

        return $this->adTagConfigs[self::FREQUENCY_CAP_KEY_OF_AD_TAG];

    }
} 