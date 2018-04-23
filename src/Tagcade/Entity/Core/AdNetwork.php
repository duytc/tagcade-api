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
    protected $libraryAdTags;
    protected $emailHookToken;
    protected $impressionCap;
    protected $networkOpportunityCap;
    protected $networkBlacklists;
    protected $networkWhiteLists;
    protected $customImpressionPixels;
    protected $expressionDescriptor;

    public function __construct()
    {
        $this->libraryAdTags = new ArrayCollection();
        $this->networkBlacklists = [];
        $this->networkWhiteLists = [];
    }
}