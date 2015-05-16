<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface AdSlotInterface extends ModelInterface
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
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int $width
     * @return self
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getHeight();

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height);

    /**
     * @return ArrayCollection
     */
    public function getAdTags();

    /**
     * @return ArrayCollection
     */
    public function getDynamicAdSlots();

}