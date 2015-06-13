<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface DynamicAdSlotInterface extends AdSlotAbstractInterface
{
    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return AdSlotAbstractInterface
     */
    public function getDefaultAdSlot();

    /**
     * @param AdSlotAbstractInterface $defaultAdSlot
     */
    public function setDefaultAdSlot($defaultAdSlot);

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

}