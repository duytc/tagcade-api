<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ChannelInterface extends ModelInterface
{
    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @return PublisherInterface|null
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @param ChannelSiteInterface[] $channelSites
     * @return self
     */
    public function setChannelSites($channelSites);

    /**
     * @return ChannelSiteInterface[]|ArrayCollection|null
     */
    public function getChannelSites();

    /**
     * @return array
     */
    public function getSites();
}
