<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class DynamicAdSlot extends AdSlotAbstract implements DynamicAdSlotInterface
{
    protected $id;

    /**
     * @var SiteInterface
     */
    protected $site;
    protected $name;
    /**
     * @var AdSlotInterface
     */
    protected $defaultAdSlot;
    protected $deletedAt;
    /**
     * @var ExpressionInterface[]
     */
    protected $expressions;

    public function __construct()
    {
        $this->expressions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getSite()
    {
        return $this->site;
    }

    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSiteId()
    {
        if (!$this->site) {
            return null;
        }

        return $this->site->getId();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * @return AdSlotInterface
     */
    public function getDefaultAdSlot()
    {
        return $this->defaultAdSlot;
    }

    /**
     * @param AdSlotInterface $defaultAdSlot
     */
    public function setDefaultAdSlot($defaultAdSlot)
    {
        $this->defaultAdSlot = $defaultAdSlot;
    }

    /**
     * @return ExpressionInterface[]
     */
    public function getExpressions()
    {
        if ($this->expressions == null) {
            $this->expressions = new ArrayCollection();
        }

        return $this->expressions;
    }

    /**
     * @param ExpressionInterface[] $expressions
     */
    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;
    }

    public function __toString()
    {
        return $this->name;
    }
}