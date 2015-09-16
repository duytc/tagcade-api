<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SiteInterface extends ModelInterface
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
     * @param string $domain
     * @return self
     */
    public function setDomain($domain);

    /**
     * @return string|null
     */
    public function getDomain();

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

    public function getReportableAdSlots();


    public function getAllAdSlots();

    /**
     * @return boolean|null
     */
    public function getEnableSourceReport();

    /**
     * @param boolean $enableSourceReport
     */
    public function setEnableSourceReport($enableSourceReport);

    /**
     * @return ArrayCollection
     */
    public function getSourceReportSiteConfigs();

    /**
     * @param ChannelSiteInterface[] $channelSites
     */
    public function setChannelSites($channelSites);

    /**
     * @return ArrayCollection
     */
    public function getChannelSites();

    /**
     * @return array
     */
    public function getChannels();

    /**
     * @return mixed
     */
    public function getPlayers();

    /**
     * @param mixed $players
     */
    public function setPlayers($players);
}
