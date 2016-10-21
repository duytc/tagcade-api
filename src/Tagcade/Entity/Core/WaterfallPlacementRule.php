<?php


namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\WaterfallPlacementRule as WaterfallPlacementRuleModel;

class WaterfallPlacementRule extends WaterfallPlacementRuleModel
{
    protected $id;
    protected $profitType;
    protected $profitValue;
    protected $publishers;
    protected $position;
    protected $priority;
    protected $rotationWeight;
    protected $waterfalls;
    protected $deletedAt;
    protected $libraryVideoDemandAdTag;
}