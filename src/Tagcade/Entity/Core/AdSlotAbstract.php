<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

abstract class AdSlotAbstract
{
    const TYPE_DISPLAY = 'display';
    const TYPE_NATIVE = 'native';
    const TYPE_DYNAMIC = 'dynamic';

    protected $id;
    /**
     * @var AdTagInterface[]
     */
    protected $adTags;
    /**
     * @var SiteInterface
     */
    protected $site;
    /**
     * @var bool
     */
    protected $autoCreate = false;
    protected $slotType;
    protected $type; // # a hack to have type output in json
    /**
     * @var BaseLibraryAdSlotInterface
     */
    protected $libraryAdSlot;
    protected $deletedAt;

    protected $deleteToken;
    protected $autoRefresh = false;
    protected $refreshEvery;
    protected $maximumRefreshTimes;

    public function __construct()
    {
        $this->adTags = new ArrayCollection();
        $this->autoCreate = false;
        $this->autoRefresh = false;
        $this->refreshEvery = 0;
        $this->maximumRefreshTimes = 0;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->slotType;
    }

    /**
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdTags()
    {
        if (null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    public function setAdTags($adTags)
    {
        $this->adTags = $adTags;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
            return $this->libraryAdSlot->getName();
        }

        return null;
    }

    public function setName($name)
    {
        if ($this->libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
            return $this->libraryAdSlot->setName($name);
        }

        return $this;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    public function setSite(SiteInterface $site)
    {
        $this->site = $site;

        return $this;
    }

    public function getLibraryAdSlot()
    {
        return $this->libraryAdSlot;
    }

    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryAdSlot = $libraryAdSlot;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCoReferencedAdSlots()
    {
        return $this->libraryAdSlot->getAdSlots();
    }

    /**
     * @return mixed
     */
    public function getSlotType()
    {
        return $this->slotType;
    }

    /**
     * @param mixed $slotType
     */
    public function setSlotType($slotType)
    {
        $this->slotType = $slotType;
    }

    /**
     * @return boolean
     */
    public function isAutoCreate()
    {
        return $this->autoCreate;
    }

    /**
     * @param boolean $autoCreate
     * @return self
     */
    public function setAutoCreate($autoCreate)
    {
        $this->autoCreate = $autoCreate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleteToken()
    {
        return $this->deleteToken;
    }

    /**
     * @param mixed $deleteToken
     */
    public function setDeleteToken($deleteToken)
    {
        $this->deleteToken = $deleteToken;
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        return $this->site->getChannels();
    }

    /**
     * @return boolean
     */
    public function isAutoRefresh()
    {
        return $this->autoRefresh;
    }

    /**
     * @param boolean $autoRefresh
     * @return self
     */
    public function setAutoRefresh($autoRefresh)
    {
        $this->autoRefresh = $autoRefresh;
    }

    /**
     * @return mixed
     */
    public function getRefreshEvery()
    {
        return $this->refreshEvery;
    }

    /**
     * @param mixed $refreshEvery
     * @return self
     */
    public function setRefreshEvery($refreshEvery)
    {
        $this->refreshEvery = $refreshEvery;
    }

    /**
     * @return mixed
     */
    public function getMaximumRefreshTimes()
    {
        return $this->maximumRefreshTimes;
    }

    /**
     * @param mixed $maximumRefreshTimes
     * @return self
     */
    public function setMaximumRefreshTimes($maximumRefreshTimes)
    {
        $this->maximumRefreshTimes = $maximumRefreshTimes;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}