<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\Collection;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AdNetwork implements AdNetworkInterface
{
    protected $id;

    /** @var UserEntityInterface */
    protected $publisher;
    protected $name;
    protected $active;
    protected $libraryAdTags;
    protected $emailHookToken;

    /**
     * This is the default CPM assigned to all ad tags unless it is overwritten
     */
    protected $defaultCpmRate;
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;

    protected $impressionCap;
    protected $networkOpportunityCap;

    /**
     * @var NetworkBlacklistInterface[]
     */
    protected $networkBlacklists;
    /**
     * @var NetworkWhiteListInterface[]
     */
    protected $networkWhiteLists;

    /**
     * @var
     */
    protected $customImpressionPixels;

    /**
     * @var array
     */
    protected $expressionDescriptor;

    /**
     * @inheritdoc
     */
    public function getDisplayBlacklists()
    {
        $networkBlacklists = $this->getNetworkBlacklists();
        $displayBlacklists = [];
        foreach ($networkBlacklists as $networkBlacklist) {
            if ($networkBlacklist->getDisplayBlacklist() instanceof DisplayBlacklistInterface) {
                $displayBlacklists[] = $networkBlacklist->getDisplayBlacklist();
            }
        }

        return $displayBlacklists;
    }

    /**
     * @return DisplayWhiteListInterface[]
     */
    public function getDisplayWhiteLists()
    {
        $networkWhiteLists = $this->getNetworkWhiteLists();
        $displayWhiteLists = [];

        foreach ($networkWhiteLists as $networkWhiteList) {
            if ($networkWhiteList->getDisplayWhiteList() instanceof DisplayWhiteListInterface) {
                $displayWhiteLists[] = $networkWhiteList->getDisplayWhiteList();
            }
        }

        return $displayWhiteLists;
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCap()
    {
        return $this->impressionCap;
    }

    /**
     * @inheritdoc
     */
    public function setImpressionCap($impressionCap)
    {
        $this->impressionCap = $impressionCap;
    }

    /**
     * @return mixed
     */
    public function getNetworkOpportunityCap()
    {
        return $this->networkOpportunityCap;
    }

    /**
     * @inheritdoc
     */
    public function setNetworkOpportunityCap($networkOpportunityCap)
    {
        $this->networkOpportunityCap = $networkOpportunityCap;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    protected function getEncryptionKey()
    {
        if (!$this->getPublisher() instanceof PublisherInterface || empty($this->getPublisher()->getUuid())) {
            throw new \Exception('Expect to have publisher and publisher uuid in order to set partner credentials');
        }

        $uuid = preg_replace('[\-]', '', $this->getPublisher()->getUuid());

        return substr($uuid, 0, 16);
    }

    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        if (!$this->publisher) {
            return null;
        }

        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher->getUser();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultCpmRate()
    {
        return $this->defaultCpmRate;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultCpmRate($defaultCpmRate)
    {
        $this->defaultCpmRate = $defaultCpmRate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getActiveAdTagsCount()
    {
        return $this->activeAdTagsCount;
    }

    /**
     * @inheritdoc
     */
    public function setActiveAdTagsCount($activeAdTagsCount)
    {
        $this->activeAdTagsCount = $activeAdTagsCount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPausedAdTagsCount()
    {
        return $this->pausedAdTagsCount;
    }

    /**
     * @inheritdoc
     */
    public function setPausedAdTagsCount($pausedAdTagsCount)
    {
        $this->pausedAdTagsCount = $pausedAdTagsCount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        $allAdTags = [];
        $tagLibs = $this->libraryAdTags->toArray();

        array_walk(
            $tagLibs,
            function (LibraryAdTagInterface $libraryAdTag) use (&$allAdTags) {
                $adTags = $libraryAdTag->getAdTags()->toArray();
                $allAdTags = array_merge($allAdTags, $adTags);
            }
        );

        return array_unique($allAdTags);
    }

    /**
     * @inheritdoc
     */
    public function getEmailHookToken()
    {
        return $this->emailHookToken;
    }

    /**
     * @inheritdoc
     */
    public function setEmailHookToken($emailHookToken)
    {
        $this->emailHookToken = $emailHookToken;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getNetworkBlacklists()
    {
        return $this->networkBlacklists;
    }

    /**
     * @inheritdoc
     */
    public function setNetworkBlacklists($networkBlacklists)
    {
        $this->networkBlacklists = $networkBlacklists;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomImpressionPixels()
    {
        return $this->customImpressionPixels;
    }

    /**
     * @inheritdoc
     */
    public function getCustomImpressionPixelsForCache()
    {
        $customImpressionPixels = $this->customImpressionPixels;
        
        $customImpressionPixelsForCache= [];
        if (is_array($customImpressionPixels)) {
            foreach ($customImpressionPixels as $customImpressionPixel) {
                if (!is_array($customImpressionPixel) || !array_key_exists('url', $customImpressionPixel)) {
                    continue;
                }

                $customImpressionPixelsForCache[] = $customImpressionPixel['url'];
            }
        }

        return $customImpressionPixelsForCache;
    }

    /**
     * @inheritdoc
     */
    public function setCustomImpressionPixels($customImpressionPixels)
    {
        $this->customImpressionPixels = $customImpressionPixels;

        return $this;
    }

    /**
     * @return NetworkWhiteListInterface[]
     */
    public function getNetworkWhiteLists()
    {
        return $this->networkWhiteLists;
    }

    /**
     * @param NetworkWhiteListInterface[] $networkWhiteLists
     * @return self
     */
    public function setNetworkWhiteLists($networkWhiteLists)
    {
        $this->networkWhiteLists = $networkWhiteLists;
        return $this;
    }

    /**
     * @return array
     */
    public function getExpressionDescriptor()
    {
        return $this->expressionDescriptor;
    }

    /**
     * @param array $expressionDescriptor
     */
    public function setExpressionDescriptor($expressionDescriptor)
    {
        $this->expressionDescriptor = $expressionDescriptor;
    }
}