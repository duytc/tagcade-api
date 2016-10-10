<?php


namespace Tagcade\Service\Core\VideoWaterfallTag;


use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRule;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Repository\Core\VideoWaterfallTagRepositoryInterface;

class VideoWaterfallTagService implements VideoWaterfallTagServiceInterface
{
    use CalculateRatiosTrait;
    /**
     * @var VideoWaterfallTagRepositoryInterface
     */
    protected $videoWaterfallTagRepository;

    /**
     * VideoWaterfallTagService constructor.
     * @param VideoWaterfallTagRepositoryInterface $videoWaterfallTagRepository
     */
    public function __construct(VideoWaterfallTagRepositoryInterface $videoWaterfallTagRepository)
    {
        $this->videoWaterfallTagRepository = $videoWaterfallTagRepository;
    }

    public function getValidVideoWaterfallTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryDemandAdTag)
    {
        $waterfallTags = [];
        $placementRules = $libraryDemandAdTag->getWaterfallPlacementRules();
        /**
         * @var WaterfallPlacementRuleInterface $placementRule
         */
        foreach($placementRules as $placementRule) {
            $tags = $this->videoWaterfallTagRepository->getWaterfallTagHaveBuyPriceLowerThanAndBelongsToListPublishers(
                $placementRule->getPublishers(),
                $this->calculateMinimumBuyPriceForPlacementRule($placementRule)
            );
            $waterfallTags = array_merge($waterfallTags, $tags);
        }

        return $waterfallTags;
    }

    /**
     * @param WaterfallPlacementRuleInterface $placementRule
     * @return null|float
     */
    protected function calculateMinimumBuyPriceForPlacementRule(WaterfallPlacementRuleInterface $placementRule)
    {
        if ($placementRule->getLibraryVideoDemandAdTag()->getSellPrice() === null) {
            return null;
        }

        switch ($placementRule->getProfitType()) {
            case WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN:
                return $placementRule->getLibraryVideoDemandAdTag()->getSellPrice() - $placementRule->getProfitValue();
            case WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN:
                return $this->getRatio($placementRule->getLibraryVideoDemandAdTag()->getSellPrice() * (100 - $placementRule->getProfitValue()), 100);
            default:
                return null;
        }
    }
}