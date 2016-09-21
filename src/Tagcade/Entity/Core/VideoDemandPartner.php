<?php


namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\VideoDemandPartner as VideoDemandPartnerModel;
use Tagcade\Model\User\UserEntityInterface;

class VideoDemandPartner extends VideoDemandPartnerModel
{
    protected $id;

    protected $name;
    protected $nameCanonical;
    protected $defaultTagURL;
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;
    protected $libraryVideoDemandAdTags;

    /** @var UserEntityInterface $publisher */
    protected $publisher;

    public function __construct()
    {
        $this->activeAdTagsCount = 0;
        $this->pausedAdTagsCount = 0;
        $this->libraryVideoDemandAdTags = new ArrayCollection();
    }
}