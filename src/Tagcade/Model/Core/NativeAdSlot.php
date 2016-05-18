<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\RTBEnabledInterface;

class NativeAdSlot extends AdSlotAbstract implements NativeAdSlotInterface, ReportableAdSlotInterface
{
    protected $id;

    function __construct()
    {
        parent::__construct();

        $this->setSlotType(AdSlotAbstract::TYPE_NATIVE);
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        if (null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    /**
     * @param ArrayCollection $adTags
     * @return $this
     */
    public function setAdTags($adTags)
    {
        $this->adTags = $adTags;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE_NATIVE;
    }

    public function isRTBEnabled()
    {
        return RTBEnabledInterface::RTB_DISABLED;
    }

    public function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            $this->getType(),
            $this->getLibraryAdSlot()->getId()
        );

        $adTags = $this->getAdTags()->toArray();

        usort($adTags, function (AdTagInterface $a, AdTagInterface $b) {
                return strcmp($a->getRefId(), $b->getRefId());
            }
        );

        /** @var AdTagInterface $t */
        foreach ($adTags as $t) {
            $array[] = $t->checkSum();
        }

        return md5(serialize($array));
    }
}