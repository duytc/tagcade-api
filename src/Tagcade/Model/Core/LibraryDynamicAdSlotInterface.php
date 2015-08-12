<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDynamicAdSlotInterface extends BaseLibraryAdSlotInterface
{
    /**
     * @return mixed
     */
    public function getNative();

    /**
     * @param mixed $native
     */
    public function setNative($native);

    /**
     * @return boolean
     */
    public function isSupportedNative();

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return PersistentCollection
     */
    public function getLibraryExpressions();

    /**
     * @param LibraryExpressionInterface[] $libraryExpressions
     */
    public function setLibraryExpressions($libraryExpressions);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getDefaultLibraryAdSlot();
    /**
     * @param BaseLibraryAdSlotInterface $defaultLibraryAdSlot
     */
    public function setDefaultLibraryAdSlot($defaultLibraryAdSlot);
}