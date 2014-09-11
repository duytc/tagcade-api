<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\AdTag as AdTagModel;

class AdTag extends AdTagModel
{
    protected $id;
    protected $adSlot;
    protected $adNetwork;
    protected $name;
    protected $html;
    protected $position;

    protected $created;
    protected $updated;

    public function __construct()
    {}

    /**
     * @return null|\DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return null|\DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}