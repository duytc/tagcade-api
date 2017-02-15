<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AdNetwork implements AdNetworkInterface
{
    protected $id;

    /** @var UserEntityInterface */
    protected $publisher;
    protected $name;
    protected $url;
    protected $active;
    protected $libraryAdTags;
    protected $emailHookToken;
    /** @var AdNetworkPartnerInterface */
    protected $networkPartner;

    /**
     * This is the default CPM assigned to all ad tags unless it is overwritten
     */
    protected $defaultCpmRate;
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;

    protected $impressionCap;
    protected $networkOpportunityCap;

    /**
     * @return mixed
     */
    public function getImpressionCap()
    {
        return $this->impressionCap;
    }

    /**
     * @param mixed $impressionCap
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
     * @param mixed $networkOpportunityCap
     */
    public function setNetworkOpportunityCap($networkOpportunityCap)
    {
        $this->networkOpportunityCap = $networkOpportunityCap;
    }


    /**
     * @return AdNetworkPartnerInterface
     */
    public function getNetworkPartner()
    {
        return $this->networkPartner;
    }

    /**
     * @param AdNetworkPartnerInterface $networkPartner
     */
    public function setNetworkPartner($networkPartner)
    {
        $this->networkPartner = $networkPartner;
    }

    /**
     * @return mixed
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return self
     */
    public function increaseActiveAdTagsCount()
    {
        if ($this->activeAdTagsCount === null || $this->activeAdTagsCount == 0) {
            $this->activeAdTagsCount = 1;
        } else ++$this->activeAdTagsCount;

        return $this;
    }

    /**
     * @return self
     */
    public function decreaseActiveAdTagsCount()
    {
        if ($this->activeAdTagsCount === null || $this->activeAdTagsCount < 2) {
            $this->activeAdTagsCount = 0;
        } else --$this->activeAdTagsCount;

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
     * @return self
     */
    public function increasePausedAdTagsCount()
    {
        if ($this->pausedAdTagsCount === null || $this->pausedAdTagsCount == 0) {
            $this->pausedAdTagsCount = 1;
        } else ++$this->pausedAdTagsCount;

        return $this;
    }

    /**
     * @return self
     */
    public function decreasePausedAdTagsCount()
    {
        if ($this->pausedAdTagsCount === null || $this->pausedAdTagsCount < 2) {
            $this->pausedAdTagsCount = 0;
        } else --$this->pausedAdTagsCount;

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
     * @return mixed
     */
    public function getEmailHookToken()
    {
        return $this->emailHookToken;
    }

    /**
     * @param mixed $emailHookToken
     * @return self
     */
    public function setEmailHookToken($emailHookToken)
    {
        $this->emailHookToken = $emailHookToken;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}