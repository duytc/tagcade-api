<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;

abstract class AdSlotAbstract
{
    const TYPE_DISPLAY = 'display';
    const TYPE_NATIVE = 'native';
    const TYPE_DYNAMIC = 'dynamic';

    protected $id;
    /**
     * @var AdTagInterface[]
     */
    protected $adTags;
    /**
     * @var SiteInterface
     */
    protected $site;
    protected $type;
    protected $name;

    protected $deletedAt;

    public function __construct()
    {
        $this->adTags = new ArrayCollection();
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
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdTags()
    {
        if (null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    /**
     * @param \Tagcade\Model\Core\AdTagInterface[] $adTags
     */
    public function setAdTags($adTags)
    {
        $this->adTags = $adTags;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param SiteInterface $site
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }


    abstract public function getLibraryAdSlot();


    abstract public function setLibraryAdSlot($libaryAdSlot);

}