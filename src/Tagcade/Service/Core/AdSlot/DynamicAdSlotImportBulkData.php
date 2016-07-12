<?php


namespace Tagcade\Service\Core\AdSlot;


use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\DomainManager\LibraryAdSlotManager;
use Tagcade\DomainManager\LibraryAdSlotManagerInterface;
use Tagcade\DomainManager\LibraryDynamicAdSlotManagerInterface;
use Tagcade\DomainManager\LibraryExpressionManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DynamicAdSlotImportBulkData implements  DynamicAdSlotImportBulkDataInterface {

    const  INDEX_KEY                                                = 'index';
    const EXPRESSION_INDEX_KEY                                      = 'expression_index';

    const INDEX_SITE_NAME_KEY                                       = 'siteName';
    const INDEX_DYNAMIC_AD_SLOT_NAME_KEY                            = 'dynamicAdSlotName';
    const INDEX_DEFAULT_AD_SLOT_KEY                                 = 'defaultAdSlot';

    const EXPRESSION_INDEX_DYNAMIC_AD_SLOT_KEY                      ='dynamicAdSlotName';
    const EXPRESSION_INDEX_EXPRESSION_AD_SLOT_KEY                   ='expressionAdSlot';
    const EXPRESSION_INDEX_START_POSITION_KEY                       ='startPosition';
    const EXPRESSION_INDEX_HEADER_BID_PRICE_KEY                     ='headerBidPrice';
    const EXPRESSION_INDEX_CONDITION_KEY                            ='condition';
    const EXPRESSION_INDEX_CONDITION_TYPE_KEY                       ='conditionType';
    const EXPRESSION_INDEX_CONDITION_VALUE_KEY                      ='conditionValue';
    const EXPRESSION_INDEX_COMPARISON_KEY                           ='comparison';
    const EXPRESSION_INDEX_EXPRESSION_KEY                           ='expression';

    const LIBRARY_DYNAMIC_AD_SLOT_PUBLISHER_KEY                     =   'publisher';
    const LIBRARY_DYNAMIC_AD_SLOT_NAME_KEY                          =   'name';
    const LIBRARY_DYNAMIC_AD_SLOT_VISIBLE_KEY                       =   'visible';
    const LIBRARY_DYNAMIC_AD_SLOT_TYPE_KEY                          =   'type';
    const LIBRARY_DYNAMIC_AD_SLOT_NATIVE_KEY                        =   'native';
    const LIBRARY_DYNAMIC_AD_SLOT_DEFAULT_LIBRARY_AD_SLOT_KEY       =   'defaultLibraryAdSlot';

    const EXPRESSION_LIBRARY_EXPRESSION_KEY                         = 'libraryExpression';
    const EXPRESSION_EXPECT_AD_SLOT_KEY                             = 'expectAdSlot';
    const EXPRESSION_DYNAMIC_AD_SLOT_KEY                            = 'dynamicAdSlot';
    const EXPRESSION_EXPRESSION_IN_JS_KEY                           = 'expressionInJs';
    const EXPRESSION_HB_BID_PRICE_KEY                               =  'hbBidPrice';

    const DYNAMIC_AD_SLOT_SITE_KEY                                  = 'site';
    const DYNAMIC_AD_SLOT_LIBRARY_AD_SLOT_KEY                       ='libraryAdSlot';
    const DYNAMIC_AD_SLOT_SLOT_TYPE                                 = 'slotType';
    const DYNAMIC_AD_SLOT_DEFAULT_AD_SLOT_KEY                       = 'defaultAdSlot';

    const LIBRARY_EXPRESSION_LIBRARY_DYNAMIC_AD_SLOT                =   'libraryDynamicAdSlot';
    const LIBRARY_EXPRESSION_EXPECT_LIBRARY_AD_SLOT                 =   'expectLibraryAdSlot';
    const LIBRARY_EXPRESSION_EXPRESSION_IN_JS                       =   'expressionInJs';
    const LIBRARY_EXPRESSION_EXPRESSION_DESCRIPTOR                  =   'expressionDescriptor';
    const LIBRARY_EXPRESSION_START_POSITION                         =   'startingPosition';

    const DYNAMIC_AD_SLOT_VISIBLE_DEFAULT_VALUE                     = false;
    const DYNAMIC_AD_SLOT_NATIVE_DEFAULT_VALUE                      = false;

    const EXPRESSION_ARRAY_GROUP_TYPE_KEY                           =   'groupType';
    const EXPRESSION_ARRAY_GROUP_VAL_KEY                            =   'groupVal';

    const GROUP_VAL_ARRAY_VAR_KEY                                   =   'var';
    const GROUP_VAL_ARRAY_CPM_KEY                                   =   'cmp';
    const GROUP_VAL_ARRAY_VAL_KEY                                   =   'val';
    const GROUP_VAL_ARRAY_TYPE_KEY                                  =  'type';


    protected static $OPERATOR      = [
        'AND'=>'AND',
        'OR'=>'OR'
    ];

    protected static $DATA_TYPE     = [
        'TEXT' =>'string',
        'NUMERIC'=>'numeric',
        'TRUE/FALSE'=>'boolean'
    ];

    protected static $DEVICES       = [
       'iOS'                =>'iPad|iPhone|iPod',
       'Android'            =>'Android',
       'Windows 10 Mobile'  =>'Windows Phone 10.0',
       'BlackBerry 10'      =>'BB10',
       'Firefox OS'         =>'Mobile',
       'Sailfish OS'        => 'Sailfish',
       'Tizen'              =>'Tizen',
       'Ubuntu Touch OS'   =>'Ubuntu'
    ];

    protected static $EXPRESSION_MAP = [
        'EQUAL TO'                  => '==',
        'NOT EQUAL TO'              => '!=',
        'LESS THAN'                 => '<',
        'LESS THAN OR EQUAL TO'     => '<=',
        'GREATER THAN'              => '>=',
        'GREATER THAN OR EQUAL TO'  => '>=',
        'IS'                        => 'is',
        'IS NOT'                    => 'isNot',
        'CONTAINS'                  => 'contains',
        'DOES NOT CONTAIN'          => 'notContains',
        'STARTS WITH'               => 'startsWith',
        'DOES NOT START WITH'       => 'notStartsWith',
        'ENDS WITH'                 => 'endsWith',
        'DOES NOT END WITH'         => 'notEndsWith'

    ];

    protected  static  $COUNTRIES = [
        'Afghanistan'=>'AF','Åland Islands'=>'AX','Albania'=>'AL','Algeria'=>'DZ','American Samoa'=>'AS','AndorrA'=>'AD','Angola'=>'AO','Anguilla'=>'AI','Antarctica'=>'AQ','Antigua and Barbuda'=>'AG','Argentina'=>'AR','Armenia'=>'AM','Aruba'=>'AW','Australia'=>'AU','Austria'=>'AT','Azerbaijan'=>'AZ','Bahamas'=>'BS','Bahrain'=>'BH','Bangladesh'=>'BD','Barbados'=>'BB','Belarus'=>'BY','Belgium'=>'BE','Belize'=>'BZ','Benin'=>'BJ','Bermuda'=>'BM','Bhutan'=>'BT','Bolivia'=>'BO','Bosnia and Herzegovina'=>'BA','Botswana'=>'BW','Bouvet Island'=>'BV','Brazil'=>'BR','British Indian Ocean Territory'=>'IO','Brunei Darussalam'=>'BN','Bulgaria'=>'BG','Burkina Faso'=>'BF','Burundi'=>'BI','Cambodia'=>'KH','Cameroon'=>'CM','Canada'=>'CA','Cape Verde'=>'CV','Cayman Islands'=>'KY','Central African Republic'=>'CF','Chad'=>'TD','Chile'=>'CL','China'=>'CN','Christmas Island'=>'CX','Cocos (Keeling) Islands'=>'CC','Colombia'=>'CO','Comoros'=>'KM','Congo'=>'CG','Congo=> The Democratic Republic of the'=>'CD','Cook Islands'=>'CK','Costa Rica'=>'CR','Cote D\'Ivoire'=>'CI','Croatia'=>'HR','Cuba'=>'CU','Cyprus'=>'CY','Czech Republic'=>'CZ','Denmark'=>'DK','Djibouti'=>'DJ','Dominica'=>'DM','Dominican Republic'=>'DO','Ecuador'=>'EC','Egypt'=>'EG','El Salvador'=>'SV','Equatorial Guinea'=>'GQ','Eritrea'=>'ER','Estonia'=>'EE','Ethiopia'=>'ET','Falkland Islands (Malvinas)'=>'FK','Faroe Islands'=>'FO','Fiji'=>'FJ','Finland'=>'FI','France'=>'FR','French Guiana'=>'GF','French Polynesia'=>'PF','French Southern Territories'=>'TF','Gabon'=>'GA','Gambia'=>'GM','Georgia'=>'GE','Germany'=>'DE','Ghana'=>'GH','Gibraltar'=>'GI','Greece'=>'GR','Greenland'=>'GL','Grenada'=>'GD','Guadeloupe'=>'GP','Guam'=>'GU','Guatemala'=>'GT','Guernsey'=>'GG','Guinea'=>'GN','Guinea-Bissau'=>'GW','Guyana'=>'GY','Haiti'=>'HT','Heard Island and Mcdonald Islands'=>'HM','Holy See (Vatican City State)'=>'VA','Honduras'=>'HN','Hong Kong'=>'HK','Hungary'=>'HU','Iceland'=>'IS','India'=>'IN','Indonesia'=>'ID','Iran=> Islamic Republic Of'=>'IR','Iraq'=>'IQ','Ireland'=>'IE','Isle of Man'=>'IM','Israel'=>'IL','Italy'=>'IT','Jamaica'=>'JM','Japan'=>'JP','Jersey'=>'JE','Jordan'=>'JO','Kazakhstan'=>'KZ','Kenya'=>'KE','Kiribati'=>'KI','Korea=> Democratic People\'S Republic of'=>'KP','Korea=> Republic of'=>'KR','Kuwait'=>'KW','Kyrgyzstan'=>'KG','Lao People\'S Democratic Republic'=>'LA','Latvia'=>'LV','Lebanon'=>'LB','Lesotho'=>'LS','Liberia'=>'LR','Libyan Arab Jamahiriya'=>'LY','Liechtenstein'=>'LI','Lithuania'=>'LT','Luxembourg'=>'LU','Macao'=>'MO','Macedonia=> The Former Yugoslav Republic of'=>'MK','Madagascar'=>'MG','Malawi'=>'MW','Malaysia'=>'MY','Maldives'=>'MV','Mali'=>'ML','Malta'=>'MT','Marshall Islands'=>'MH','Martinique'=>'MQ','Mauritania'=>'MR','Mauritius'=>'MU','Mayotte'=>'YT','Mexico'=>'MX','Micronesia=> Federated States of'=>'FM','Moldova=> Republic of'=>'MD','Monaco'=>'MC','Mongolia'=>'MN','Montserrat'=>'MS','Morocco'=>'MA','Mozambique'=>'MZ','Myanmar'=>'MM','Namibia'=>'NA','Nauru'=>'NR','Nepal'=>'NP','Netherlands'=>'NL','Netherlands Antilles'=>'AN','New Caledonia'=>'NC','New Zealand'=>'NZ','Nicaragua'=>'NI','Niger'=>'NE','Nigeria'=>'NG','Niue'=>'NU','Norfolk Island'=>'NF','Northern Mariana Islands'=>'MP','Norway'=>'NO','Oman'=>'OM','Pakistan'=>'PK','Palau'=>'PW','Palestinian Territory=> Occupied'=>'PS','Panama'=>'PA','Papua New Guinea'=>'PG','Paraguay'=>'PY','Peru'=>'PE','Philippines'=>'PH','Pitcairn'=>'PN','Poland'=>'PL','Portugal'=>'PT','Puerto Rico'=>'PR','Qatar'=>'QA','Reunion'=>'RE','Romania'=>'RO','Russian Federation'=>'RU','RWANDA'=>'RW','Saint Helena'=>'SH','Saint Kitts and Nevis'=>'KN','Saint Lucia'=>'LC','Saint Pierre and Miquelon'=>'PM','Saint Vincent and the Grenadines'=>'VC','Samoa'=>'WS','San Marino'=>'SM','Sao Tome and Principe'=>'ST','Saudi Arabia'=>'SA','Senegal'=>'SN','Serbia and Montenegro'=>'CS','Seychelles'=>'SC','Sierra Leone'=>'SL','Singapore'=>'SG','Slovakia'=>'SK','Slovenia'=>'SI','Solomon Islands'=>'SB','Somalia'=>'SO','South Africa'=>'ZA','South Georgia and the South Sandwich Islands'=>'GS','Spain'=>'ES','Sri Lanka'=>'LK','Sudan'=>'SD','Suriname'=>'SR','Svalbard and Jan Mayen'=>'SJ','Swaziland'=>'SZ','Sweden'=>'SE','Switzerland'=>'CH','Syrian Arab Republic'=>'SY','Taiwan, Province of China'=>'TW','Tajikistan'=>'TJ','Tanzania=> United Republic of'=>'TZ','Thailand'=>'TH','Timor-Leste'=>'TL','Togo'=>'TG','Tokelau'=>'TK','Tonga'=>'TO','Trinidad and Tobago'=>'TT','Tunisia'=>'TN','Turkey'=>'TR','Turkmenistan'=>'TM','Turks and Caicos Islands'=>'TC','Tuvalu'=>'TV','Uganda'=>'UG','Ukraine'=>'UA','United Arab Emirates'=>'AE','United Kingdom'=>'GB','United States'=>'US','United States Minor Outlying Islands'=>'UM','Uruguay'=>'UY','Uzbekistan'=>'UZ','Vanuatu'=>'VU','Venezuela'=>'VE','Viet Nam'=>'VN','Virgin Islands, British'=>'VG','Virgin Islands, U.S.'=>'VI','Wallis and Futuna'=>'WF','Western Sahara'=>'EH','Yemen'=>'YE','Zambia'=>'ZM','Zimbabwe'=>'ZW'
    ];

    /**
     * @var
     */
    private $dynamicAdSlotConfig;
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;
    /**
     * @var DynamicAdSlotManagerInterface
     */
    private $dynamicAdSlotManager;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var LibraryExpressionManagerInterface
     */
    private $libraryExpressionManager;
    /**
     * @var LibraryAdSlotManagerInterface
     */
    private $libraryAdSlotManager;
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var ExpressionInJsGenerator
     */
    private $expressionInJsGenerator;

    function __construct(DynamicAdSlotManagerInterface $dynamicAdSlotManager,
                         SiteManagerInterface $siteManager, LibraryExpressionManagerInterface $libraryExpressionManager,
                         LibraryAdSlotManagerInterface $libraryAdSlotManager, AdSlotManagerInterface $adSlotManager,
                         EntityManager $entityManager, ExpressionInJsGenerator $expressionInJsGenerator,
                         Logger $logger, $dynamicAdSlotConfig)
    {
        $this->dynamicAdSlotConfig = $dynamicAdSlotConfig;
        $this->siteManager = $siteManager;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
        $this->logger = $logger;
        $this->libraryExpressionManager = $libraryExpressionManager;
        $this->libraryAdSlotManager = $libraryAdSlotManager;
        $this->adSlotManager = $adSlotManager;
        $this->entityManager = $entityManager;
        $this->expressionInJsGenerator = $expressionInJsGenerator;
    }

    /**
     * @param array $dynamicAdSlots
     * @param array $expressions
     * @param Site $siteObject
     * @param $displayAdSlotsObject
     * @param $dryOption
     * @return array|mixed
     * @throws \Exception
     */
    public function importDynamicAdSlots(array $dynamicAdSlots, array $expressions, Site $siteObject, $displayAdSlotsObject, $dryOption )
    {
        $publisher = $siteObject->getPublisher();
        $fullFillExpressions = $this->makeFullForShortTypeExpression($expressions);
        $dynamicAdSlotObjects = [];

        foreach ($dynamicAdSlots as $dynamicAdSlot) {

            $allExpressionsAdSlotNames = $this->findAllExpressionAdSlotNamesForOneDynamicAdSlot($dynamicAdSlot[$this->getNameIndexOfDynamicAdSlot()], $fullFillExpressions);
            $defaultAdSlotName = $dynamicAdSlot[$this->getDefaultAdSlotIndexOfDynamicAdSlot()];
            $dynamicAdSlotName = $dynamicAdSlot[$this->getNameIndexOfDynamicAdSlot()];

            /** @var ReportableAdSlotInterface $defaultAdSlot */
            $defaultAdSlot = $this->getDefaultAdSlot($defaultAdSlotName, $displayAdSlotsObject);
            $defaultLibraryAdSlot = $this->getDefaultLibraryAdSlot($defaultAdSlotName, $displayAdSlotsObject);

            $libraryDynamicAdSlot = $this->createLibraryDynamicAdSlot ($publisher, $dynamicAdSlot, $defaultLibraryAdSlot);
            $dynamicAdSlotObject  = $this->createDynamicAdSlot($libraryDynamicAdSlot,$defaultAdSlot, $siteObject);

            $expressions =[];
            foreach ($allExpressionsAdSlotNames as $expressionsAdSlotName) {
                $expressionForOneExpectAdSlots = $this->findAllExpressionsForOneExpressionAdSlot($dynamicAdSlotName, $expressionsAdSlotName, $fullFillExpressions);
                $libraryExpression = $this->createLibraryExpressionAdSlot($expressionForOneExpectAdSlots, $expressionsAdSlotName,$libraryDynamicAdSlot, $displayAdSlotsObject);
                $expectAdSlot = $this->getExpectAdSlot($expressionsAdSlotName,$displayAdSlotsObject);
                $expression= $this->createDynamicAdSlotExpression($libraryExpression, $dynamicAdSlotObject, $expressionForOneExpectAdSlots, $expectAdSlot);
                $expressions[]  = $expression;
            }
            $dynamicAdSlotObject->setExpressions($expressions);

            $dynamicAdSlotObjects [] = $dynamicAdSlotObject;
        }

        if (true == $dryOption) {
            $this->logger->info(sprintf('       Total dynamic ad slots import: %d', count($dynamicAdSlotObjects)));
        }

        return $dynamicAdSlotObjects;
    }

    /**
     * @param PublisherInterface $publisher
     * @param $dynamicAdSlot
     * @param $defaultLibraryAdSlot
     * @return LibraryDynamicAdSlot
     * @throws \Exception
     */
    protected function createLibraryDynamicAdSlot(PublisherInterface $publisher, $dynamicAdSlot, $defaultLibraryAdSlot)
    {
        $libraryDynamicAdSlot = [];

        $libraryDynamicAdSlot[self::LIBRARY_DYNAMIC_AD_SLOT_PUBLISHER_KEY]                = $publisher;
        $libraryDynamicAdSlot[self::LIBRARY_DYNAMIC_AD_SLOT_NAME_KEY]                     = $dynamicAdSlot[$this->getNameIndexOfDynamicAdSlot()];
        $libraryDynamicAdSlot[self::LIBRARY_DYNAMIC_AD_SLOT_VISIBLE_KEY]                  = self::DYNAMIC_AD_SLOT_VISIBLE_DEFAULT_VALUE;
        $libraryDynamicAdSlot[self::LIBRARY_DYNAMIC_AD_SLOT_TYPE_KEY]                     = LibraryDynamicAdSlot::TYPE_DYNAMIC;
        $libraryDynamicAdSlot[self::LIBRARY_DYNAMIC_AD_SLOT_NATIVE_KEY]                   = self::DYNAMIC_AD_SLOT_NATIVE_DEFAULT_VALUE;
        $libraryDynamicAdSlot[self::LIBRARY_DYNAMIC_AD_SLOT_DEFAULT_LIBRARY_AD_SLOT_KEY]  = $defaultLibraryAdSlot;

        $libraryDynamicAdSlotObject = LibraryDynamicAdSlot::createLibraryDynamicAdSlotFromArray($libraryDynamicAdSlot);

        return $libraryDynamicAdSlotObject;
    }

    /**
     * @param $expressionForOneExpectAdSlots
     * @param $expressionsAdSlotName
     * @param $libraryDynamicAdSlot
     * @param $displayAdSlotObjects
     * @return LibraryExpression
     * @throws \Exception
     */
    protected function createLibraryExpressionAdSlot($expressionForOneExpectAdSlots, $expressionsAdSlotName, $libraryDynamicAdSlot, $displayAdSlotObjects)
    {
        $libraryExpression = [];
        $expectLibraryAdSlot= $this->getExpectLibraryAdSlot($expressionsAdSlotName, $displayAdSlotObjects);
        $expressionDescriptor = $this->buildExpressionDescriptorObject($expressionForOneExpectAdSlots);
        $expressionDescriptorInJs = $this->buildExpressionDescriptorObjectInJs($expressionDescriptor);

        $libraryExpression[self::LIBRARY_EXPRESSION_LIBRARY_DYNAMIC_AD_SLOT]        = $libraryDynamicAdSlot;
        $libraryExpression[self::LIBRARY_EXPRESSION_EXPECT_LIBRARY_AD_SLOT]         = $expectLibraryAdSlot;
        $libraryExpression[self::LIBRARY_EXPRESSION_EXPRESSION_IN_JS]               = $expressionDescriptorInJs;
        $libraryExpression[self::LIBRARY_EXPRESSION_EXPRESSION_DESCRIPTOR]          = $expressionDescriptor;
        $libraryExpression[self::LIBRARY_EXPRESSION_START_POSITION]                 = $expressionForOneExpectAdSlots[0][$this->getStartPositionIndexOfExpression()];

        $libraryExpressionObject =  LibraryExpression::createLibraryExpression($libraryExpression);

        return $libraryExpressionObject;

    }

    /**
     * @param $defaultAdSlotName
     * @param $displayAdSlots
     * @return DisplayAdSlotInterface
     */

    protected function getDefaultAdSlot($defaultAdSlotName, $displayAdSlots)
    {
        /** @var DisplayAdSlotInterface $displayAdSlot */
        foreach ($displayAdSlots as $displayAdSlot) {
            if(0 == strcmp($displayAdSlot->getName(),$defaultAdSlotName)) {
                return $displayAdSlot;
            }
        }
    }

    /**
     * @param $defaultAdSlotName
     * @param $displayAdSlots
     * @return \Tagcade\Model\Core\BaseLibraryAdSlotInterface
     */
    protected function getDefaultLibraryAdSlot($defaultAdSlotName, $displayAdSlots)
    {
        $defaultAdSlot = $this->getDefaultAdSlot($defaultAdSlotName,$displayAdSlots);
        return $defaultAdSlot->getLibraryAdSlot();
    }

    /**
     * @param $expressionsAdSlotName
     * @param $displayAdSlotObjects
     * @return DisplayAdSlotInterface
     */

    protected function getExpectAdSlot($expressionsAdSlotName, $displayAdSlotObjects)
    {
        /** @var DisplayAdSlotInterface $displayAdSlotObject */
        foreach ($displayAdSlotObjects as $displayAdSlotObject) {
            if( 0== strcmp($displayAdSlotObject->getName(), $expressionsAdSlotName)) {
                return $displayAdSlotObject;
            }
        }
    }

    /**
     * @param $expressionsAdSlotName
     * @param $displayAdSlotObjects
     * @return \Tagcade\Model\Core\BaseLibraryAdSlotInterface
     */
    protected function getExpectLibraryAdSlot($expressionsAdSlotName, $displayAdSlotObjects)
    {
        $expectAdSlot = $this->getExpectAdSlot($expressionsAdSlotName, $displayAdSlotObjects);
        return $expectAdSlot->getLibraryAdSlot();
    }

    /**
     * @param $libraryExpression
     * @param $dynamicAdSlotObject
     * @param $expressionForOneExpectAdSlots
     * @param $expectAdSlot
     * @return Expression
     * @throws \Exception
     */
    protected function createDynamicAdSlotExpression($libraryExpression, $dynamicAdSlotObject, $expressionForOneExpectAdSlots, $expectAdSlot)
    {
        $expressionDescriptor = $this->buildExpressionDescriptorObject($expressionForOneExpectAdSlots);
        $expressionDescriptorInJs = $this->buildExpressionDescriptorObjectInJs($expressionDescriptor);

        $dynamicExpression = [];
        $dynamicExpression[self::EXPRESSION_LIBRARY_EXPRESSION_KEY] = $libraryExpression;
        $dynamicExpression[self::EXPRESSION_EXPECT_AD_SLOT_KEY] = $expectAdSlot;
        $dynamicExpression[self::EXPRESSION_DYNAMIC_AD_SLOT_KEY] = $dynamicAdSlotObject;
        $dynamicExpression[self::EXPRESSION_HB_BID_PRICE_KEY] = $expressionForOneExpectAdSlots[0][$this->getHeaderBidPriceIndexOfExpression()];
        $dynamicExpression[self::EXPRESSION_EXPRESSION_IN_JS_KEY]   = $expressionDescriptorInJs;

        $expression =  Expression::createExpressionFromArray($dynamicExpression);

        return $expression;
    }

    /**
     * @param $libraryDynamicAdSlot
     * @param $defaultAdSlot
     * @param $site
     * @return DynamicAdSlot
     */
    protected function createDynamicAdSlot($libraryDynamicAdSlot, $defaultAdSlot, $site)
    {
        $dynamicAdSlot = [];

        $dynamicAdSlot[self::DYNAMIC_AD_SLOT_SITE_KEY]                   = $site;
        $dynamicAdSlot[self::DYNAMIC_AD_SLOT_LIBRARY_AD_SLOT_KEY]        = $libraryDynamicAdSlot;
        $dynamicAdSlot[self::DYNAMIC_AD_SLOT_SLOT_TYPE]                  = DynamicAdSlot::TYPE_DYNAMIC;
        $dynamicAdSlot[self::DYNAMIC_AD_SLOT_DEFAULT_AD_SLOT_KEY]        = $defaultAdSlot;

        $dynamicAdSlotObject =  DynamicAdSlot::createDynamicAdSlotFromArray($dynamicAdSlot);

        return $dynamicAdSlotObject;
    }
    /**
     * @param array $expressions
     * @return array
     */
    protected function makeFullForShortTypeExpression( array $expressions)
    {
        $lastExpression =[];

        foreach ($expressions as $mainKey => $expression) {
            if(!empty ($expression[$this->getDynamicAdSlotNameIndexOfExpression()])) {
                $lastExpression = $expression;
            } else {
                foreach ($expression as $key=>$value) {
                    if (empty($value)) {
                        $expression[$key] = $lastExpression[$key];
                        $expressions[$mainKey] = $expression;
                    }
                }
            }
        }

        return $expressions;
    }

    /**
     * @param $dynamicAdSlotName
     * @param array $expressions
     * @return array
     * @throws \Exception
     */
    protected function findAllExpressionAdSlotNamesForOneDynamicAdSlot($dynamicAdSlotName, array $expressions)
    {
        $expectExpressionsAdSlotNames = [];
        foreach ($expressions as $expression) {
            if( 0 == strcmp($dynamicAdSlotName, $expression[$this->getDynamicAdSlotNameIndexOfExpression()])) {
                $expectExpressionsAdSlotNames[] = $expression[$this->getExpressionAdSlotIndexOfExpression()];
            }
        }

        return  array_unique($expectExpressionsAdSlotNames);
    }

    /**
     * @param $dynamicAdSlotName
     * @param $expressionAdSlotName
     * @param array $expressions
     * @return array
     * @throws \Exception
     */
    protected function findAllExpressionsForOneExpressionAdSlot($dynamicAdSlotName, $expressionAdSlotName, array $expressions)
    {
        $expectExpressions = [];
        foreach ($expressions as $expression) {
            if( 0 == strcmp($dynamicAdSlotName, $expression[$this->getDynamicAdSlotNameIndexOfExpression()])
                && 0 == strcmp($expressionAdSlotName, $expression[$this->getExpressionAdSlotIndexOfExpression()]) ) {
                $expectExpressions[] = $expression;
            }
        }

        return $expectExpressions;
    }

    /**
     * @param $descriptor
     * @return array
     */
    protected function buildExpressionDescriptorObjectInJs($descriptor)
    {
        $expressionDescriptorObjectInJs = $this->expressionInJsGenerator->generateExpressionInJsFromDescriptor($descriptor);

        return $expressionDescriptorObjectInJs;
    }

    /**
     * @param array $expressionForOneExpectAdSlots
     * @return array
     * @throws \Exception
     */
    protected function buildExpressionDescriptorObject(array $expressionForOneExpectAdSlots)
    {
        $expressionDescriptorObject =[];

        $groupValueObjects=[];
        foreach ($expressionForOneExpectAdSlots as $expression)
        {
            $condition = $expression[$this->getConditionIndexOfExpression()];
            $conditionType = $expression[$this->getConditionTypeIndexOfExpression()];
            $conditionComparison = $expression[$this->getComparisonIndexOfExpression()];
            $conditionValue = $expression[$this->getConditionValueIndexOfExpression()];

            $groupValueObject = [];
            $groupValueObject[self::GROUP_VAL_ARRAY_VAR_KEY] = $condition;
            $groupValueObject[self::GROUP_VAL_ARRAY_TYPE_KEY] = $this->convertConditionType($conditionType);
            $groupValueObject[self::GROUP_VAL_ARRAY_CPM_KEY] =  $this->convertComparisonType($conditionComparison);
            $groupValueObject[self::GROUP_VAL_ARRAY_VAL_KEY] = $this->convertConditionValue($condition,$conditionValue);

            $groupValueObjects[] = $groupValueObject;
        }

        $groupType = $expressionForOneExpectAdSlots[0][$this->getExpressionValueIndexOfExpression()];
        $expressionDescriptorObject[self::EXPRESSION_ARRAY_GROUP_VAL_KEY] = $groupValueObjects;
        $expressionDescriptorObject[self::EXPRESSION_ARRAY_GROUP_TYPE_KEY] = $this->convertGroupType($groupType);

        return $expressionDescriptorObject;
    }

    /**
     * @param $groupType
     * @return mixed
     * @throws \Exception
     */
    protected function convertGroupType($groupType)
    {
        if (!array_key_exists($groupType, self::$OPERATOR))
        {
            throw new \Exception(sprintf('Expression %s it not support', $groupType));
        }

        return self::$OPERATOR[$groupType];
    }

    /**
     * @param $conditionType
     * @return mixed
     * @throws \Exception
     */
    protected function convertConditionType($conditionType)
    {
        if (!array_key_exists($conditionType, self::$DATA_TYPE))
        {
            throw new \Exception(sprintf('Condition type %s it not support', $conditionType));
        }

        return self::$DATA_TYPE[$conditionType];
    }

    /**
     * @param $comparisonType
     * @return mixed
     * @throws \Exception
     */
    protected function convertComparisonType ($comparisonType)
    {
        $comparisonType = strtoupper($comparisonType);

        if (!array_key_exists($comparisonType, self::$EXPRESSION_MAP))
        {
            throw new \Exception(sprintf('Condition type %s it not support', $comparisonType));
        }

        return self::$EXPRESSION_MAP[$comparisonType];
    }

    /**
     * @param $condition
     * @param $conditionValue
     * @return mixed
     * @throws \Exception
     */
    protected function convertConditionValue ($condition , $conditionValue)
    {
        if ( $condition == '${DEVICE}') {
            if (array_key_exists($conditionValue, self::$DEVICES)) {
               return self::$DEVICES[$conditionValue];
            } else {
                $this->logger->warning(sprintf('Invalid value =%s for variable %s ', $conditionValue, $condition ));
                throw new \Exception(sprintf('Invalid value =%s for variable %s ', $conditionValue, $condition ));
            }
        }

        if ($condition == '${COUNTRY}'){

            $countriesNames = explode(',', $conditionValue);
            $countryCodes =[];
            foreach ($countriesNames  as $countriesName) {
                $countriesName = trim($countriesName);
                if (array_key_exists($countriesName, self::$COUNTRIES)) {
                    $countryCodes[] = self::$COUNTRIES[$countriesName];
                } else {
                    $this->logger->warning(sprintf('Invalid value =%s for variable %s ', $conditionValue, $condition ));
                    throw new \Exception(sprintf('Invalid value =%s for variable %s ', $conditionValue, $condition ));
                }
            }

            return implode(',',$countryCodes);
        }

        return $conditionValue;
    }

    /**
     * @param $countryName
     * @return mixed
     */
    protected function getCountryCode($countryName)
    {
        return self::$COUNTRIES[$countryName];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getSiteNameIndexOfDynamicAdSlot()
    {
        if(!array_key_exists(self::INDEX_SITE_NAME_KEY, $this->dynamicAdSlotConfig[self::INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::INDEX_SITE_NAME_KEY, $this->dynamicAdSlotConfig[self::INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::INDEX_KEY][self::INDEX_SITE_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getNameIndexOfDynamicAdSlot()
    {

        if(!array_key_exists(self::INDEX_DYNAMIC_AD_SLOT_NAME_KEY, $this->dynamicAdSlotConfig[self::INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::INDEX_DYNAMIC_AD_SLOT_NAME_KEY, $this->dynamicAdSlotConfig[self::INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::INDEX_KEY][self::INDEX_DYNAMIC_AD_SLOT_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getDefaultAdSlotIndexOfDynamicAdSlot()
    {
        if(!array_key_exists(self::INDEX_DEFAULT_AD_SLOT_KEY, $this->dynamicAdSlotConfig[self::INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::INDEX_DEFAULT_AD_SLOT_KEY, $this->dynamicAdSlotConfig[self::INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::INDEX_KEY][self::INDEX_DEFAULT_AD_SLOT_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getDynamicAdSlotNameIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_DYNAMIC_AD_SLOT_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_DYNAMIC_AD_SLOT_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_DYNAMIC_AD_SLOT_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getExpressionAdSlotIndexOfExpression()
    {

        if(!array_key_exists(self::EXPRESSION_INDEX_EXPRESSION_AD_SLOT_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_EXPRESSION_AD_SLOT_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_EXPRESSION_AD_SLOT_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getStartPositionIndexOfExpression()
    {

        if(!array_key_exists(self::EXPRESSION_INDEX_START_POSITION_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_START_POSITION_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_START_POSITION_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getHeaderBidPriceIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_HEADER_BID_PRICE_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_HEADER_BID_PRICE_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_HEADER_BID_PRICE_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getConditionIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_CONDITION_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_CONDITION_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_CONDITION_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getConditionTypeIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_CONDITION_TYPE_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_CONDITION_TYPE_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_CONDITION_TYPE_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getComparisonIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_COMPARISON_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_COMPARISON_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_COMPARISON_KEY];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getConditionValueIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_CONDITION_VALUE_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string %s',
                self::EXPRESSION_INDEX_CONDITION_VALUE_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_CONDITION_VALUE_KEY];
    }

    protected function getExpressionValueIndexOfExpression()
    {
        if(!array_key_exists(self::EXPRESSION_INDEX_EXPRESSION_KEY, $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY]))
        {
            throw new \Exception (sprintf('There is not %s key in config string',
                self::EXPRESSION_INDEX_EXPRESSION_KEY));
        }

        return $this->dynamicAdSlotConfig[self::EXPRESSION_INDEX_KEY][self::EXPRESSION_INDEX_EXPRESSION_KEY];

    }

}