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
     * @return string
     */
    public function checkSum();

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot();


    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return $this
     */
    public function setLibraryAdSlot($libraryAdSlot);

}