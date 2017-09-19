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
     * @return mixed
     */
    public function getImpressionCap();

    /**
     * @param mixed $impressionCap
     * @return self
     */
    public function setImpressionCap($impressionCap);

    /**
     * @return mixed
     */
    public function getNetworkOpportunityCap();

    /**
     * @param mixed $networkOpportunityCap
     * @return self
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

    /**
     * @return NetworkBlacklistInterface[]
     */
    public function getNetworkBlacklists();

    /**
     * @param NetworkBlacklistInterface[] $networkBlacklists
     * @return self
     */
    public function setNetworkBlacklists($networkBlacklists);

    /**
     * @return NetworkWhiteListInterface[]
     */
    public function getNetworkWhiteLists();

    /**
     * @param NetworkWhiteListInterface[] $networkWhiteLists
     * @return self
     */
    public function setNetworkWhiteLists($networkWhiteLists);
    /**
     * @return DisplayBlacklistInterface[]
     */
    public function getDisplayBlacklists();

    /**
     * @return DisplayWhiteListInterface[]
     */
    public function getDisplayWhiteLists();

    /**
     * @return array|null
     */
    public function getCustomImpressionPixels();
    
    /**
     * parse from format
     * [ { "url" => "<url-1>" }, { "url" => "<url-2>" }, ... ]
     * to format
     * [ "<url-1>", "<url-2>", ... ]
     *
     * @return array|null
     */
    public function getCustomImpressionPixelsForCache();

    /**
     * @param array|null $customImpressionPixels
     * @return self
     */
    public function setCustomImpressionPixels($customImpressionPixels);

    /**
     * @return array
     */
    public function getExpressionDescriptor();

    /**
     * @param array $expressionDescriptor
     */
    public function setExpressionDescriptor($expressionDescriptor);
}