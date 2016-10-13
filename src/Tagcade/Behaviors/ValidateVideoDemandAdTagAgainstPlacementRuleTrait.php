<?php


namespace Tagcade\Behaviors;


use Tagcade\Entity\Core\WaterfallPlacementRule;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\CalculateRatiosTrait;

trait ValidateVideoDemandAdTagAgainstPlacementRuleTrait
{
    use CalculateRatiosTrait;

    protected function validateDemandAdTagAgainstPlacementRule(VideoDemandAdTagInterface $demandAdTag)
    {
        $libraryVideoDemandAdTag = $demandAdTag->getLibraryVideoDemandAdTag();
        $waterfallTag = $demandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag();
        $rule = $demandAdTag->getWaterfallPlacementRule();

        $publishers = $rule->getPublishers();
        if (!empty($publishers)) {
            if (!in_array($waterfallTag->getVideoPublisher()->getId(), $publishers)) {
                return false;
            }
        }

        if ($libraryVideoDemandAdTag->getSellPrice() === null) {
            return true;
        }

        if ($waterfallTag->getBuyPrice() === null) {
            return true;
        }

        switch ($rule->getProfitType()) {
            case WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN:
                return $libraryVideoDemandAdTag->getSellPrice() - $waterfallTag->getBuyPrice() >= $rule->getProfitValue();
            case WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN:
                return (1 - $this->getRatio($waterfallTag->getBuyPrice(), $libraryVideoDemandAdTag->getSellPrice())) * 100 >= $rule->getProfitValue();
            default:
                return true;
        }
    }
}