<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdSlotLibAbstract;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class LibraryNativeAdSlot extends LibraryAdSlotAbstract implements LibraryNativeAdSlotInterface, ReportableLibraryAdSlotInterface
{
    protected $id;
    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $defaultDynamicAdSlots;
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }


    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots()
    {
        return $this->defaultDynamicAdSlots;
    }

    /**
     * Calculate CheckSum string of an given library AdSlot
     * by concatenating major properties together with null value ignored, then returning the MD5 hash
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            parent::TYPE_NATIVE,
            $this->getId()
        );

        $adTags = $this->getLibSlotTags()->toArray();

        usort($adTags, function(LibrarySlotTagInterface $a, LibrarySlotTagInterface $b) {
            return strcmp($a->getRefId(), $b->getRefId());
        });

        /** @var LibrarySlotTagInterface $t */
        foreach($adTags as $t){
            $array[] =  $t->checkSum();
        }

        return md5(serialize($array));
    }

    public function __toString()
    {
        return parent::__toString();
    }
}