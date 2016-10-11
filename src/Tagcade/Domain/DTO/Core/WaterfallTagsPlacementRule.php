<?php


namespace Tagcade\Domain\DTO\Core;


use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;

class WaterfallTagsPlacementRule
{
    /**
     * @var WaterfallPlacementRuleInterface
     */
    protected $placementRule;

    /**
     * @var VideoWaterfallTagInterface[]
     */
    protected $waterfallTags;

    /**
     * WaterfallTagsPlacementRule constructor.
     * @param WaterfallPlacementRuleInterface $placementRule
     * @param VideoWaterfallTagInterface[] $waterfallTags
     */
    public function __construct(WaterfallPlacementRuleInterface $placementRule, array $waterfallTags)
    {
        $this->placementRule = $placementRule;
        $this->waterfallTags = $waterfallTags;
    }

    /**
     * @return WaterfallPlacementRuleInterface
     */
    public function getPlacementRule()
    {
        return $this->placementRule;
    }

    /**
     * @return \Tagcade\Model\Core\VideoWaterfallTagInterface[]
     */
    public function getWaterfallTags()
    {
        return array_map(function(VideoWaterfallTagInterface $waterfallTag){
            return $waterfallTag->getId();
        }, $this->waterfallTags);
    }
}