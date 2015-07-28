<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

abstract class LibraryAdSlotAbstract
{
    const TYPE_DISPLAY = 'display';
    const TYPE_NATIVE = 'native';
    const TYPE_DYNAMIC = 'dynamic';

    protected $id;
    protected $referenceName;
    protected $visible = false;
    protected $libType;
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    protected $deletedAt;

    function __construct($referenceName)
    {
        $this->referenceName = $referenceName;
    }

    function __toString()
    {
        return $this->referenceName;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }
    /**
     * @param boolean $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param PublisherInterface $publisher
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @return int|null
     */
    public function getPublisherId(){
        return $this->publisher->getId();
    }

    /**
     * @return mixed
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * @param mixed $referenceName
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;
    }

    /**
     * @return mixed
     */
    public function getLibType()
    {
        return $this->libType;
    }

    /**
     * @param mixed $libType
     */
    public function setLibType($libType)
    {
        $this->libType = $libType;
    }

}