<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\AdTag as AdTagModel;
use DateTime;

class AdTag extends AdTagModel
{
    protected $id;
    protected $adSlot;
    protected $adNetwork;
    protected $name;
    protected $html;
    protected $position;
    protected $active;

    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    public function __construct()
    {}

    /**
     * @return null|DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return null|DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}