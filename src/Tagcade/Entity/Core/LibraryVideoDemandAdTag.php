<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryVideoDemandAdTag as LibraryVideoDemandAdTagModel;

class LibraryVideoDemandAdTag extends LibraryVideoDemandAdTagModel
{
    protected $id;

    protected $tagURL;
    protected $name;
    protected $timeout;
    protected $targeting;
    protected $sellPrice;
    protected $deletedAt;
    protected $videoDemandPartner;
    protected $videoDemandAdTags;
    protected $waterfallPlacementRules;
}