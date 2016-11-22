<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\VideoDemandAdTag as VideoDemandAdTagModel;

class VideoDemandAdTag extends VideoDemandAdTagModel
{
    protected $id;
    protected $priority;
    protected $rotationWeight;
    protected $active;
    protected $deletedAt;
    protected $targeting;
    protected $requestCap;
    protected $libraryVideoDemandAdTag;
    protected $videoWaterfallTagItem;
    protected $targetingOverride;
    protected $waterfallPlacementRule;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    function __construct()
    {
        parent::__construct();
    }
}