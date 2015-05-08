<?php

namespace Tagcade\Entity\Core;

use DateTime;
use Tagcade\Model\Core\AdTag as AdTagModel;

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
    protected $frequencyCap;

    /** int - for rotation display AdTags */
    protected $rotation;
    /** int - type of AdTags*/
    protected $adType;
    /** array - json_array, descriptor of AdTag*/
    protected $descriptor;

    public function __construct()
    {
    }

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