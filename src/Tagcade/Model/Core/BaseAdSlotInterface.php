<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface BaseAdSlotInterface extends ModelInterface
{
    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return SiteInterface|null
     */
    public function getSite();

    /**
     * @param SiteInterface $site
     * @return self
     */
    public function setSite(SiteInterface $site);

    /**
     * @return mixed
     */
    public function getType();

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
     * @return ArrayCollection
     */
    public function getAdTags();

    /**
     * @return BaseAdSlotInterface[]
     */
    public function getCoReferencedAdSlots();

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot();


    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return $this
     */
    public function setLibraryAdSlot($libraryAdSlot);

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
     * @return mixed
     */
    public function getDeleteToken();

    /**
     * @param mixed $deleteToken
     */
    public function setDeleteToken($deleteToken);

    /**
     * @return array
     */
    public function getChannels();

    /**
     * @return boolean
     */
    public function isAutoRefresh();

    /**
     * @param boolean $autoRefresh
     */
    public function setAutoRefresh($autoRefresh);

    /**
     * @return mixed
     */
    public function getRefreshEvery();

    /**
     * @param mixed $refreshEvery
     */
    public function setRefreshEvery($refreshEvery);

    /**
     * @return mixed
     */
    public function getMaximumRefreshTimes();

    /**
     * @param mixed $maximumRefreshTimes
     */
    public function setMaximumRefreshTimes($maximumRefreshTimes);

    public function getDeletedAt();
}