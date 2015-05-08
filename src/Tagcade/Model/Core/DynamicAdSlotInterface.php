<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface DynamicAdSlotInterface extends ModelInterface
{
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
     * @return int|null
     */
    public function getSiteId();


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
     * @return AdSlotInterface
     */
    public function getDefaultAdSlot();

    /**
     * @param AdSlotInterface $defaultAdSlot
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

}