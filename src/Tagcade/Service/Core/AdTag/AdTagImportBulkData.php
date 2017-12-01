<?php


namespace Tagcade\Service\Core\AdTag;


use Monolog\Logger;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\LibraryAdTagManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagImportBulkData implements AdTagImportBulkDataInterface
{
    const SITE_SHEET_NAME = 'Sites';
    const AD_TAGS_SHEET_NAME = 'Ad Tags';
    const DISPLAY_AD_SLOT_SHEET_NAME = 'Display Ad Slots';
    const DYNAMIC_AD_SLOT_NAME = 'Dynamic Ad Slots';
    const EXPRESSION_TARGETING_NAME = 'Expression Targeting';

    const POSITION_KEY_OF_AD_TAG = 'position';
    const ACTIVE_KEY_OF_AD_TAG = 'active';
    const FREQUENCY_CAP_KEY_OF_AD_TAG = 'frequencyCap';
    const ROTATION_KEY_OF_AD_TAG = 'rotation';
    const IMPRESSION_CAP_KEY_OF_AD_TAG = 'impressionCap';
    const NETWORK_OPPORTUNITY_CAP_KEY_OF_AD_TAG = 'networkOpportunityCap';
    const LIBRARY_AD_TAG_KEY_OF_AD_TAG = 'libraryAdTag';
    const AD_SLOT_KEY_OF_AD_TAG = 'adSlot';
    const REF_ID_KEY_OF_AD_TAG = 'refId';

    const NAME_KEY_OF_LIB_AD_TAG = 'name';
    const HTML_KEY_OF_LIB_AD_TAG = 'html';
    const VISIBLE_KEY_OF_LIB_AD_TAG = 'visible';
    const AD_TYPE_KEY_OF_LIB_AD_TAG = 'adType';
    const AD_NETWORK_KEY_OF_LIB_AD_TAG = 'adNetwork';
    const PARTNER_TAG_ID_KEY_OF_AD_TAG = 'partnerTagId';

    const HTML_DEFAULT_VALUE_OF_LIB_AD_TAG = null;
    const VISIBLE_DEFAULT_VALUE_OF_LIB_AD_TAG = false;
    const AD_TYPE_DEFAULT_VALUE_OF_LIB_AD_TAG = 0;
    const PARTNER_TAG_ID_DEFAULT_VALUE_OF_AD_TAG = null;

    const AD_SLOT_NAME_KEY = 'adSlotName';
    const AD_TAG_NAME_KEY = 'adTagName';

    const POSITION_DEFAULT_VALUE = 1;
    const ACTIVE_DEFAULT_VALUE = 1;
    const FREQUENCY_CAP_DEFAULT_VALUE = null;
    const ROTATION_DEFAULT_VALUE = null;
    const IMPRESSION_CAP_DEFAULT_VALUE = null;
    const NETWORK_OPPORTUNITY_CAP_DEFAULT_VALUE = null;

    private $libraryAdTagObjectsInOneSection = [];
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

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
    /**
     * @var LibraryAdTagManagerInterface
     */
    private $libraryAdTagManager;

    function __construct(AdNetworkManagerInterface $adNetworkManager, Logger $logger, $adTagConfigs,
                         LibraryAdTagManagerInterface $libraryAdTagManager, AdTagManagerInterface $adTagManager)
    {
        $this->adTagConfigs = $adTagConfigs;
        $this->adNetworkManager = $adNetworkManager;
        $this->logger = $logger;
        $this->libraryAdTagManager = $libraryAdTagManager;
        $this->adTagManager = $adTagManager;
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
        $libraryAdSlotAdTags = [];
        $adTagObjectsInSystemOfAdSlot = [];
        $numberOfNewAdTags = 0;
        $numberOfExitedAdTags = 0;

        $publisher = $displayAdSlotObject->getSite()->getPublisher();
        $allAdTags = $this->createAllAdTagsData($allAdTags, $publisher);
        $isLibraryDisplayAdSlot = $displayAdSlotObject->getLibraryAdSlot()->isVisible() ? true : false;

        $adTagsOfThisAdSlotInSystem = $this->adTagManager->getAdTagsForAdSlot($displayAdSlotObject);
        foreach ($adTagsOfThisAdSlotInSystem as $adTag) {
            $md5 = md5($adTag->getHtml());
            $adTagObjectsInSystemOfAdSlot[$md5] = $adTag;
        }

        foreach ($allAdTags as $adTag) {
            $adTag[self::AD_SLOT_KEY_OF_AD_TAG] = $displayAdSlotObject;
            /** @var LibraryAdTagInterface $libraryAdTag */
            $libraryAdTag = $adTag[self::LIBRARY_AD_TAG_KEY_OF_AD_TAG];
            $htmlValue = $libraryAdTag->getHtml();
            $md5OfHtmlTag = md5($htmlValue);

            if ((!array_key_exists($md5OfHtmlTag, $adTagObjects)) && (!array_key_exists($md5OfHtmlTag, $adTagObjectsInSystemOfAdSlot))) {
                $adTagObject = AdTag::createAdTagFromArray($adTag);
                $adTagObjects[$md5OfHtmlTag] = $adTagObject;

                if (true == $isLibraryDisplayAdSlot) {
                    $libraryAdSlotAdTags[] = $this->makeLibraryAdSlotAdTag($adTagObject, $displayAdSlotObject);
                }
                $numberOfNewAdTags++;
            } else {
                $numberOfExitedAdTags++;
            }

        }

        $displayAdSlotObject->getLibraryAdSlot()->setLibSlotTags($libraryAdSlotAdTags);

        if (true == $dryOption) {
            $this->logger->info(sprintf('       Total new ad tags imported: %d, total existed ad tag: %d in display ad slot: %s', $numberOfNewAdTags, $numberOfExitedAdTags, $displayAdSlotObject->getName()));
        }

        return $adTagObjects;
    }

    /**
     * @param AdTagInterface $adTag
     * @param DisplayAdSlotInterface $displayAdSlot
     * @return LibrarySlotTag
     */
    public function makeLibraryAdSlotAdTag(AdTagInterface $adTag, DisplayAdSlotInterface $displayAdSlot)
    {
        $librarySlotTag = new LibrarySlotTag();

        $librarySlotTag->setActive($adTag->isActive());
        $librarySlotTag->setRotation($adTag->getRotation());
        $librarySlotTag->setPosition($adTag->getPosition());
        $librarySlotTag->setFrequencyCap($adTag->getFrequencyCap());
        $librarySlotTag->setLibraryAdSlot($displayAdSlot->getLibraryAdSlot());
        $librarySlotTag->setLibraryAdTag($adTag->getLibraryAdTag());
        $librarySlotTag->setRefId($adTag->getRefId());

        return $librarySlotTag;
    }

    /**
     * @param $excelRows
     * @param PublisherInterface $publisher
     * @return array
     */
    public function createAllAdTagsData($excelRows, PublisherInterface $publisher)
    {
        $allAdTagsData = [];

        foreach ($excelRows as $excelRow) {
            $adTagData = $this->createAdTagDataFromExcelRow($excelRow, $publisher);
            $allAdTagsData[] = $adTagData;
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
        // here get adtag name
        $adTagNameValue = $this->getAdTagNameValue($excelRow);

        $libraryAdTagData = $this->createLibraryAdTagDataFromExcelRow($excelRow, $publisher);

        $htmlOfLibraryAdTag = $libraryAdTagData[self::HTML_KEY_OF_LIB_AD_TAG];
        $md5OfHtml = md5($htmlOfLibraryAdTag);
        $displayAdSlotName = $excelRow[$this->getAdSlotNameIndex()];

        $libraryAdTagObjects = $this->getLibraryAdTagObjectsInOneSection();

        if (!array_key_exists($md5OfHtml, $libraryAdTagObjects)) {
            /**@var LibraryAdTagInterface[] $libraryAdTagInSystem */
            $libraryAdTagInSystem = $this->libraryAdTagManager->getLibraryAdTagsByHtml($htmlOfLibraryAdTag);
            if (empty($libraryAdTagInSystem) || count($libraryAdTagInSystem) > 1) {
                $libraryAdTagObject = LibraryAdTag::createAdTagLibraryFromArray($libraryAdTagData);
                $libraryAdTagObject->setName($displayAdSlotName); //Temporary set name to check below
                $this->insertLibraryAdTagObjectsInOneSection($libraryAdTagObject, $md5OfHtml);
            } else {
                $libraryAdTagObject = array_shift($libraryAdTagInSystem);
                /**@var AdTagInterface[] $adTagsHaveTheSameLibAdTag */
                $adTagsHaveTheSameLibAdTag = $this->adTagManager->getAdTagsHaveTheSameAdTabLib($libraryAdTagObject);
                foreach ($adTagsHaveTheSameLibAdTag as $adTagSame) {
                    if (0 != strcmp($adTagSame->getAdSlot()->getName(), $displayAdSlotName)) {
                        $libraryAdTagObject->setVisible(true);
                        break;
                    }
                }
            }

        } else {
            /**@var LibraryAdTagInterface[] $libraryAdTagObjects */
            $libraryAdTagObject = $libraryAdTagObjects[$md5OfHtml];
            $displayAdSlotNameInBuffer = $libraryAdTagObject->getName(); //Get name to set library or no
            if (0 != strcmp($displayAdSlotName, $displayAdSlotNameInBuffer)) {
                $libraryAdTagObject->setVisible(true);
            }
        }

        //$adNetWorkName = $libraryAdTagObject->getAdNetwork()->getName();
        // Before we use $adNewWorkName to set libraryAdTagName, Now no need to use it, we can use AdTagName
        $libraryAdTagObject->setName($adTagNameValue);

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
        if (empty($demandPartnerName)) {
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

        $libraryAdTag[self::NAME_KEY_OF_LIB_AD_TAG] = $adTagNameValue;
        $libraryAdTag[self::AD_NETWORK_KEY_OF_LIB_AD_TAG] = $adNetworkValue;
        $libraryAdTag[self::HTML_KEY_OF_LIB_AD_TAG] = $htmlValue;
        $libraryAdTag[self::VISIBLE_KEY_OF_LIB_AD_TAG] = $visibleValue;
        $libraryAdTag[self::AD_TYPE_KEY_OF_LIB_AD_TAG] = $adTypeValue;

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

        if (null == $adNetworks) {
            throw new \Exception('Not found demand partner in system!');
        }

        foreach ($adNetworks as $adNetwork) {
            if (0 == strcmp($demandPartName, $adNetwork->getName())) {
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
            if (0 == strcmp($adTag[$this->getAdSlotNameIndex()], $adSlotName)) {
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
     * @return array
     */
    public function getLibraryAdTagObjectsInOneSection()
    {
        return $this->libraryAdTagObjectsInOneSection;
    }

    /**
     * @param $libraryAdTagObjectsInOneSection
     * @param $md5Value
     */
    public function insertLibraryAdTagObjectsInOneSection($libraryAdTagObjectsInOneSection, $md5Value)
    {
        $this->libraryAdTagObjectsInOneSection [$md5Value] = $libraryAdTagObjectsInOneSection;
    }

    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getPartnerTagIdForAdTagLibrary($rawAdTag)
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
            return is_int($rawAdTag[$this->getOpportunityCapIndex()]) ? $rawAdTag[$this->getOpportunityCapIndex()] : self::NETWORK_OPPORTUNITY_CAP_DEFAULT_VALUE;
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
            return is_int($rawAdTag[$this->getImpressionCapIndex()]) ? $rawAdTag[$this->getImpressionCapIndex()] : self::IMPRESSION_CAP_DEFAULT_VALUE;
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
        if (array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getRotationIndex(), $rawAdTag)) {
            return is_int($rawAdTag[$this->getRotationIndex()]) ? $rawAdTag[$this->getRotationIndex()] : self:: ROTATION_DEFAULT_VALUE;
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
        if (array_key_exists(self::FREQUENCY_CAP_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getFrequencyCapIndex(), $rawAdTag)) {
            return is_int($rawAdTag[$this->getFrequencyCapIndex()]) ? $rawAdTag[$this->getFrequencyCapIndex()] : self::FREQUENCY_CAP_DEFAULT_VALUE;
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
        if (array_key_exists(self::POSITION_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getPositionIndex(), $rawAdTag)) {
            return is_int($rawAdTag[$this->getPositionIndex()]) ? $rawAdTag[$this->getPositionIndex()] : self::POSITION_DEFAULT_VALUE;
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
        if (array_key_exists(self::ACTIVE_KEY_OF_AD_TAG, $this->adTagConfigs) && array_key_exists($this->getActiveIndex(), $rawAdTag)) {
            return $rawAdTag[$this->getActiveIndex()];
        }
        return self:: ACTIVE_DEFAULT_VALUE;

    }

    /**
     * @param $rawAdTag
     * @return null
     * @throws \Exception
     */
    protected function getAdTagNameValue($rawAdTag)
    {
        return $rawAdTag[$this->getAdTagNameIndex()];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAdTagNameIndex()
    {
        if (!array_key_exists(self::AD_TAG_NAME_KEY, $this->adTagConfigs)) {
            throw new \Exception ('There is not ad Tag Name in config file');
        }

        return $this->adTagConfigs[self::AD_TAG_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAdSlotNameIndex()
    {
        if (!array_key_exists(self::AD_SLOT_NAME_KEY, $this->adTagConfigs)) {
            throw new \Exception ('There is not ad slot in config file');
        }

        return $this->adTagConfigs[self::AD_SLOT_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getDemandPartnerIndex()
    {
        if (!array_key_exists(self::AD_NETWORK_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
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
        if (!array_key_exists(self::HTML_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not tag html in config file');
        }

        return $this->adTagConfigs[self::HTML_KEY_OF_LIB_AD_TAG];
    }

    protected function getHtmlValue($rawAdTag)
    {
        if (!array_key_exists(self::HTML_KEY_OF_LIB_AD_TAG, $this->adTagConfigs)) {
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
        if (!array_key_exists(self::POSITION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
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
        if (!array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
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
        if (!array_key_exists(self::ACTIVE_KEY_OF_AD_TAG, $this->adTagConfigs)) {
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
        if (!array_key_exists(self::ROTATION_KEY_OF_AD_TAG, $this->adTagConfigs)) {
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
    protected function getPartnerTagIdIndex()
    {
        if (!array_key_exists(self::PARTNER_TAG_ID_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not partner tag id key in config file');
        }

        return $this->adTagConfigs[self::PARTNER_TAG_ID_KEY_OF_AD_TAG];

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getFrequencyCapIndex()
    {
        if (!array_key_exists(self::FREQUENCY_CAP_KEY_OF_AD_TAG, $this->adTagConfigs)) {
            throw new \Exception ('There is not partner tag id key in config file');
        }

        return $this->adTagConfigs[self::FREQUENCY_CAP_KEY_OF_AD_TAG];

    }
}