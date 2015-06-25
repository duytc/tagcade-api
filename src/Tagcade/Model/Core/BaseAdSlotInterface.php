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

    public function getType();

}