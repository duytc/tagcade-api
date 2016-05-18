<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface BillingConfigurationInterface extends ModelInterface
{
    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher($publisher);

    /**
     * @return mixed
     */
    public function getModule();

    /**
     * @param mixed $module
     * @return self
     */
    public function setModule($module);

    /**
     * @return mixed
     */
    public function getBillingFactor();

    /**
     * @param mixed $billingFactor
     * @return self
     */
    public function setBillingFactor($billingFactor);

    /**
     * @return array
     */
    public function getTiers();

    /**
     * @param array $tiers
     * @return self
     */
    public function setTiers($tiers);

    /**
     * get the CPM rate based on a given weight (slot opportunities, video impressions, visit etc )
     * @param $weight
     * @return float
     */
    public function getCpmRate($weight);

    /**
     * @return mixed
     */
    public function getDefaultConfig();

    /**
     * @param mixed $defaultConfig
     * @return self
     */
    public function setDefaultConfig($defaultConfig);


    /**
     * @return boolean
     */
    public function isDefaultConfiguration();
}