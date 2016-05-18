<?php

namespace Tagcade\Model\Core;

use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDynamicAdSlotInterface extends BaseLibraryAdSlotInterface
{
    /**
     * @return mixed
     */
    public function getNative();

    /**
     * @param mixed $native
     * @return self
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
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return PersistentCollection
     */
    public function getLibraryExpressions();

    /**
     * @param LibraryExpressionInterface[] $libraryExpressions
     * @return self
     */
    public function setLibraryExpressions($libraryExpressions);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getDefaultLibraryAdSlot();

    /**
     * @param BaseLibraryAdSlotInterface $defaultLibraryAdSlot
     * @return self
     */
    public function setDefaultLibraryAdSlot($defaultLibraryAdSlot);
}