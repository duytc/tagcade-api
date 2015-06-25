<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface DynamicAdSlotInterface extends BaseAdSlotInterface
{
    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot();

    /**
     * @param BaseAdSlotInterface $defaultAdSlot
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