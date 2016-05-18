<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\AdNetwork as AdNetworkModel;
use Tagcade\Model\Core\AdNetworkPartnerInterface;

class AdNetwork extends AdNetworkModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $url;
    protected $defaultCpmRate;
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;
    protected $libraryAdTags;
    protected $username;
    protected $impressionCap;
    protected $networkOpportunityCap;

    /**
     * @var AdNetworkPartnerInterface
     */
    protected $networkPartner;

    public function __construct()
    {
        $this->activeAdTagsCount = 0;
        $this->pausedAdTagsCount = 0;
        $this->activeAdTagsCount = 0;
        $this->libraryAdTags = new ArrayCollection();
    }
}