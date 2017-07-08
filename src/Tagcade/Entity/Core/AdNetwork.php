<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\AdNetwork as AdNetworkModel;

class AdNetwork extends AdNetworkModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $defaultCpmRate;
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;
    protected $libraryAdTags;
    protected $emailHookToken;
    protected $impressionCap;
    protected $networkOpportunityCap;
    protected $networkBlacklists;
    protected $networkWhiteLists;
    protected $customImpressionPixels;

    public function __construct()
    {
        $this->activeAdTagsCount = 0;
        $this->pausedAdTagsCount = 0;
        $this->libraryAdTags = new ArrayCollection();
        $this->networkBlacklists = [];
        $this->networkWhiteLists = [];
    }
}