<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdTag as entity;

class AdTag implements AdTagInterface
{
    protected $id;

    /** @var BaseAdSlotInterface */
    protected $adSlot;
    protected $position;
    protected $active;
    protected $frequencyCap;

    /** int - for rotation display AdTags */
    protected $rotation;
    protected $deletedAt;

    /** @var LibraryAdTagInterface */
    protected $libraryAdTag;
    protected $refId;

    /** string for mapping tag size of ad network with vs network partner */
    protected $partnerTagSize;

    protected $_autoIncreasePosition; // temp var

    /**
     * @var integer
     */
    protected $impressionCap;
    /**
     * @var integer
     */
    protected $networkOpportunityCap;

    /**
     * @var boolean
     */
    protected $passback;

    protected $expressionDescriptor;
    /**
     * @param LibraryAdTagInterface $libraryAdTag
     */
    public function __construct(LibraryAdTagInterface $libraryAdTag = null)
    {
        if ($libraryAdTag) {
            $this->html = $libraryAdTag->getHtml();

            $this->setAdNetwork($libraryAdTag->getAdNetwork());
        }

        $this->passback = false;
        $this->active = 1;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotId()
    {
        if (!$this->adSlot) {
            return null;
        }

        return $this->adSlot->getId();
    }

    public function setAdSlot(BaseAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetwork()
    {
        if ($this->libraryAdTag === null) return null;

        return $this->libraryAdTag->getAdNetwork();
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworkId()
    {
        $libraryAdTag = $this->getLibraryAdTag();

        if ($libraryAdTag == null) return null;

        /**
         * @var AdNetworkInterface $adNetwork
         */
        $adNetwork = $libraryAdTag->getAdNetwork();

        if ($adNetwork == null) return null;

        return $adNetwork->getId();
    }

    public function setAdNetwork(AdNetworkInterface $adNetwork)
    {
        if ($this->libraryAdTag != null) {
            $this->libraryAdTag->setAdNetwork($adNetwork);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if ($this->libraryAdTag instanceof LibraryAdTagInterface) {
            return $this->libraryAdTag->getName();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        if ($this->libraryAdTag instanceof LibraryAdTagInterface) {
            return $this->libraryAdTag->setName($name);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        if ($this->libraryAdTag == null) return null;

        $type = $this->libraryAdTag->getAdType();
        $html = $this->libraryAdTag->getHtml();
        switch ($type) {
            case LibraryAdTag::AD_TYPE_THIRD_PARTY:
            case LibraryAdTag::AD_TYPE_IMAGE:
                break;

            case LibraryAdTag::AD_TYPE_IN_BANNER:
                // update required fields due to adSlot and this adTag
                if ($this->adSlot instanceof DisplayAdSlotInterface) {
                    $html = str_replace('$$DATA-PV-WIDTH$$', sprintf('data-pv-width="%d"', $this->adSlot->getWidth()), $html);
                    $html = str_replace('$$DATA-PV-HEIGHT$$', sprintf('data-pv-height="%d"', $this->adSlot->getHeight()), $html);
                    $html = str_replace('$$DATA-PV-SLOT$$', sprintf('data-pv-slot="%d"', $this->adSlot->getId()), $html);
                    $html = str_replace('$$DATA-PV-TAG$$', sprintf('data-pv-tag="%d"', $this->getId()), $html);
                }
        }

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        if ($this->libraryAdTag != null) {
            $this->libraryAdTag->setHtml($html);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return $this->active == AdTagInterface::ACTIVE;
    }

    public function isAutoPaused()
    {
        return $this->active == AdTagInterface::AUTO_PAUSED;
    }

    public function activate()
    {
        $this->setActive(AdTagInterface::ACTIVE);
    }


    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setActive($boolean)
    {
        switch ($boolean) {
            case AdTagInterface::ACTIVE:
            case AdTagInterface::PAUSED:
            case AdTagInterface::AUTO_PAUSED:
                $this->active = $boolean;
                break;
            default:
                $this->active = AdTagInterface::PAUSED;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFrequencyCap($frequencyCap)
    {
        $this->frequencyCap = $frequencyCap;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFrequencyCap()
    {
        return $this->frequencyCap;
    }

    /**
     * @inheritdoc
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @inheritdoc
     */
    public function getLibraryAdTag()
    {
        return $this->libraryAdTag;
    }

    /**
     * @inheritdoc
     */
    public function setLibraryAdTag($libraryAdTag)
    {
        $this->libraryAdTag = $libraryAdTag;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCoReferencedAdTags()
    {
        if ($this->getLibraryAdTag() == null) return new ArrayCollection();

        return $this->getLibraryAdTag()->getAdTags();
    }

    /**
     * @inheritdoc
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @inheritdoc
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isInLibrary()
    {
        return ($this->libraryAdTag instanceof LibraryAdTagInterface && $this->libraryAdTag->getVisible());
    }


    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Calculate CheckSum string of an given WaterfallTag
     * by concatenating major properties together with null value ignored, then returning the MD5 hash
     * @return string
     */
    public function checkSum()
    {
        return md5(serialize(
            array(
                $this->getLibraryAdTag()->getId(),
                $this->getName(),
                $this->getPosition(),
                $this->isActive(),
                $this->getFrequencyCap(),
                $this->getRotation(),
                $this->getRefId()
            )));
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return $this->getAdSlot();
    }

    /**
     * @inheritdoc
     */
    public function getClassName()
    {
        return entity::class;
    }

    /**
     * @inheritdoc
     */
    public function getSiblings()
    {
        return $this->getAdSlot()->getAdTags();
    }

    /**
     * @return int
     */
    public function getImpressionCap()
    {
        return $this->impressionCap;
    }

    /**
     * @param int $impressionCap
     * @return self
     */
    public function setImpressionCap($impressionCap)
    {
        $this->impressionCap = $impressionCap;
        return $this;
    }

    /**
     * @return int
     */
    public function getNetworkOpportunityCap()
    {
        return $this->networkOpportunityCap;
    }

    /**
     * @param int $networkOpportunityCap
     * @return self
     */
    public function setNetworkOpportunityCap($networkOpportunityCap)
    {
        $this->networkOpportunityCap = $networkOpportunityCap;
        return $this;
    }


    /**
     * @inheritdoc
     */
    public function getAutoIncreasePosition()
    {
        return $this->_autoIncreasePosition;
    }

    /**
     * @inheritdoc
     */
    public function setAutoIncreasePosition($autoIncreasePosition)
    {
        $this->_autoIncreasePosition = $autoIncreasePosition;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function isPassback()
    {
        return $this->passback;
    }

    /**
     * @inheritdoc
     */
    public function setPassback($passback)
    {
        $this->passback = $passback;
    }
}