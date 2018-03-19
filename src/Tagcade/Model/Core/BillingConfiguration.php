<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class BillingConfiguration implements BillingConfigurationInterface
{
    const BILLING_FACTOR_SLOT_OPPORTUNITY = 'SLOT_OPPORTUNITY';
    const BILLING_FACTOR_IMPRESSION_OPPORTUNITY = 'IMPRESSION_OPPORTUNITY';
    const BILLING_FACTOR_VIDEO_IMPRESSION = 'VIDEO_IMPRESSION';
    const BILLING_FACTOR_VIDEO_VISIT = 'VISIT';
    const BILLING_FACTOR_HEADER_BID_REQUEST = 'BID_REQUEST';
    const BILLING_FACTOR_IN_BANNER_IMPRESSION = 'VIDEO_AD_IMPRESSION';

    const THRESHOLD_KEY = 'threshold';
    const CPM_KEY = 'cpmRate';

    protected $id;
    /** @var UserEntityInterface */
    protected $publisher;
    protected $module;
    protected $billingFactor;
    protected $defaultConfig;


    /**
     * @var string
     */
    //protected $tiers = [];
    protected $tiers;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return UserEntityInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param UserEntityInterface $publisher
     * @return self
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     * @return self
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingFactor()
    {
        return $this->billingFactor;
    }

    /**
     * @param mixed $billingFactor
     * @return self
     */
    public function setBillingFactor($billingFactor)
    {
        $this->billingFactor = $billingFactor;
        return $this;
    }

    /**
     * @return array
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * @param array $tiers
     * @return self
     */
    public function setTiers($tiers)
    {
        $this->tiers = $tiers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    /**
     * @param mixed $defaultConfig
     * @return self
     */
    public function setDefaultConfig($defaultConfig)
    {
        $this->defaultConfig = $defaultConfig;

        return $this;
    }

    public function getPublisherId()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getCpmRate($weight)
    {
        $tiers = $this->getTiers();
        $convertedTiers = [];
        foreach ($tiers as $tier) {
            $convertedTiers[$tier[self::THRESHOLD_KEY]] = $tier[self::CPM_KEY];
        }

        krsort($convertedTiers);

        foreach($convertedTiers as $key=>$value){
            if($key <= $weight ){
                return $value;
            }
        }

        throw new \Exception("Not found proper value");
    }

    /**
     * @return bool
     */
    public function isDefaultConfiguration()
    {
           return ($this->defaultConfig === true);
    }
}