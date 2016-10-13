<?php


namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\WaterfallPlacementRule as WaterfallPlacementRuleModel;

class WaterfallPlacementRule extends WaterfallPlacementRuleModel
{
    protected $id;
    protected $profitType;
    protected $profitValue;
    protected $publishers;
    protected $deletedAt;
    protected $libraryVideoDemandAdTag;
}