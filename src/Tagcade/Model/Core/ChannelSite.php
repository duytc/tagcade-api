<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ChannelSite implements ChannelSiteInterface
{
    protected $id;

    /** @var ChannelInterface */
    protected $channel;

    /** @var SiteInterface */
    protected $site;

    protected $deletedAt;

    /**
     * default construct
     */
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
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @inheritdoc
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @inheritdoc
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function __toString()
    {
        return $this->getChannel()->getId() . '-' . $this->getSite()->getId();
    }
}
