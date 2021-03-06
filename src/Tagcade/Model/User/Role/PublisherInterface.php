<?php

namespace Tagcade\Model\User\Role;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\BillingConfiguration;

interface PublisherInterface extends UserRoleInterface
{
    const IS_2ND_LOGIN = 'is2ndLogin';
    
    /**
     * @return string
     */
    public function getUuid();

    /**
     * @param string $uuid
     * @return self
     */
    public function setUuid($uuid);

    /**
     * @return self
     */
    public function generateAndAssignUuid();
    /**
     * @return float
     */
    public function getBillingRate();

    /**
     * @param float $billingRate
     */
    public function setBillingRate($billingRate);

    public function getFirstName();

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return mixed
     */
    public function getLastName();

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName);

    /**
     * @return mixed
     */
    public function getCompany();

    /**
     * @param mixed $company
     */
    public function setCompany($company);

    /**
     * @return mixed
     */
    public function getPhone();

    /**
     * @param mixed $phone
     */
    public function setPhone($phone);

    /**
     * @return mixed
     */
    public function getCity();

    /**
     * @param mixed $city
     */
    public function setCity($city);

    /**
     * @return mixed
     */
    public function getState();

    /**
     * @param mixed $state
     */
    public function setState($state);

    /**
     * @return mixed
     */
    public function getAddress();

    /**
     * @param mixed $address
     */
    public function setAddress($address);

    /**
     * @return mixed
     */
    public function getPostalCode();

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode);

    /**
     * @return mixed
     */
    public function getCountry();

    /**
     * @param mixed $country
     */
    public function setCountry($country);

    /**
     * @return array $enableSourceReport
     */
    public function getEnabledModules();

    /**
     * @param array $enabledModules
     */
    public function setEnabledModules(array $enabledModules);

    /**
     * @return bool
     */
    public function hasAnalyticsModule();

    /**
     * @return bool
     */
    public function hasVideoModule();

    /**
     * @return bool
     */
    public function hasUnifiedReportModule();

    /**
     * @return bool
     */
    public function hasHeaderBiddingModule();

    /**
     * @return bool
     */
    public function hasInBannerModule();

    /**
     * @return bool
     */
    public function hasDisplayModule();

    public function getEmail();

    /**
     * @return mixed
     */
    public function getSettings();

    /**
     * @param mixed $settings
     */
    public function setSettings($settings);

    /**
     * @return string
     */
    public function getTagDomain();

    /**
     * @param string $tagDomain
     * @return self
     *
     */
    public function setTagDomain($tagDomain);

    /**
     * @return mixed
     */
    public function getExchanges();

    /**
     * @param array $exchanges
     * @return self
     */
    public function setExchanges($exchanges);

    /**
     * @return mixed
     */
    public function getBidders();

    /**
     * @param array $bidders
     * @return self
     */
    public function setBidders($bidders);

    /**
     * @return boolean
     */
    public function isTestAccount();

    /**
     * @param boolean $testAccount
     * @return self
     */
    public function setTestAccount($testAccount);

    /**
     * @return mixed
     */
    public function getBillingConfigs();

    /**
     * @param mixed $billingConfigs
     */
    public function setBillingConfigs($billingConfigs);

    /**
     * @param BillingConfiguration $billingConfiguration
     * @return $this
     */
    public function addBillingConfig(BillingConfiguration $billingConfiguration);

    /**
     * @return array
     */
    public function getSubPublishers();

    /**
     * @param ArrayCollection $subPublishers
     * @return self
     */
    public function setSubPublishers($subPublishers);

    /**
     * @return PublisherInterface|null
     */
    public function getMasterAccount();

    /**
     * @param PublisherInterface|null $masterAccount
     * @return self
     */
    public function setMasterAccount(PublisherInterface $masterAccount = null);

    /**
     * @return array
     */
    public function getEmailSendAlert();

    /**
     * @param $emailSendAlert
     * @return
     */
    public function setEmailSendAlert($emailSendAlert);

}