<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkInterface extends ModelInterface
{
    /**
     * @return PublisherInterface|null
     */
    public function getPublisher();

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getUrl();

    /**
     * @param string $url
     * @return self
     */
    public function setUrl($url);

    /**
     * @return float
     */
    public function getDefaultCpmRate();

    /**
     * @param float $defaultCpmRate
     * @return $this
     */
    public function setDefaultCpmRate($defaultCpmRate);

    /**
     * @return array
     */
    public function getAdTags();

    /**
     * get number of active ad tags
     * @return int
     */
    public function getActiveAdTagsCount();

    /**
     * set number of active ad tags
     * @param int $activeAdTagsCount
     * @return self
     */
    public function setActiveAdTagsCount($activeAdTagsCount);

    /**
     * @return self
     */
    public function increaseActiveAdTagsCount();

    /**
     * @return self
     */
    public function decreaseActiveAdTagsCount();

    /**
     * get number of paused ad tags
     * @return int
     */
    public function getPausedAdTagsCount();

    /**
     * set number of paused ad tags
     * @param int $pausedAdTagsCount
     * @return self
     */
    public function setPausedAdTagsCount($pausedAdTagsCount);

    /**
     * @return self
     */
    public function increasePausedAdTagsCount();

    /**
     * @return self
     */
    public function decreasePausedAdTagsCount();


    /**
     * @return AdNetworkPartnerInterface
     */
    public function getNetworkPartner();

    /**
     * @param AdNetworkPartnerInterface $networkPartner
     */
    public function setNetworkPartner($networkPartner);

    /**
     * @return mixed
     */
    public function getImpressionCap();

    /**
     * @param mixed $impressionCap
     */
    public function setImpressionCap($impressionCap);

    /**
     * @return mixed
     */
    public function getNetworkOpportunityCap();

    /**
     * @param mixed $networkOpportunityCap
     */
    public function setNetworkOpportunityCap($networkOpportunityCap);

    /**
     * @return mixed
     */
    public function getEmailHookToken();

    /**
     * @param mixed $emailHookToken
     * @return self
     */
    public function setEmailHookToken($emailHookToken);

}