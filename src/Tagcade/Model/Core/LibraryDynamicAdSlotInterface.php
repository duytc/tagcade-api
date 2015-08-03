<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @return ArrayCollection
     */
    public function getExpressions();
    /**
     * @param ExpressionInterface[] $expressions
     */
    public function setExpressions($expressions);

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
     * @return mixed
     */
    public function getPublisherId();

    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot();

    /**
     * @param BaseAdSlotInterface $defaultAdSlot
     */
    public function setDefaultAdSlot($defaultAdSlot);
}