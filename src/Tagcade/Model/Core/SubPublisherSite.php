<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherSite implements SubPublisherSiteInterface
{
    /*
     * about access: using 3 bit as x(READ) y(WRITE) z(DELETE)
     * e.g:
     * 4 = (binary)(100) ~ READ - -
     */
    const ACCESS_READ_ONLY = 4;

    static $ACCESS_READ_ARRAY = [self::ACCESS_READ_ONLY];

    protected $id;

    /** @var SubPublisherInterface */
    protected $subPublisher;

    /** @var SiteInterface */
    protected $site;

    /**
     * @var integer
     * value as permission in linux, e.g 1 = READ, 2 = WRITE, 3 = READ+WRITE, ...
     */
    protected $access;

    protected $deletedAt;

    /**
     * default construct
     */
    public function __construct()
    {
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
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @inheritdoc
     */
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @inheritdoc
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function __toString()
    {
        return $this->getSubPublisher()->getId() . '-' . $this->getSite()->getId();
    }
}
