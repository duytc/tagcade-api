<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

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

    /**
     * @return array|ReportableAdSlotInterface[]
     */
    public function getReportableAdSlots();

    /**
     * @return array|DisplayAdSlotInterface[]
     */
    public function getDisplayAdSlots();

    /**
     * @param $adSlots
     * @return self
     */
    public function setAdSlots($adSlots);

    /**
     * @return ArrayCollection
     */
    public function getAllAdSlots();

    /**
     * @return boolean|null
     */
    public function getEnableSourceReport();

    /**
     * @param boolean $enableSourceReport
     * @return self
     */
    public function setEnableSourceReport($enableSourceReport);

    /**
     * @return ArrayCollection
     */
    public function getSourceReportSiteConfigs();

    /**
     * @param ChannelSiteInterface[] $channelSites
     * @return self
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
     * @return self
     */
    public function setPlayers($players);

    /**
     * @return boolean
     */
    public function isAutoCreate();

    /**
     * @param boolean $autoCreate
     * @return self
     */
    public function setAutoCreate($autoCreate);

    /**
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getSiteToken();

    /**
     * @param mixed $siteToken
     * @return self
     */
    public function setSiteToken($siteToken);

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @param mixed $subPublisher
     */
    public function setSubPublisher($subPublisher);
}
