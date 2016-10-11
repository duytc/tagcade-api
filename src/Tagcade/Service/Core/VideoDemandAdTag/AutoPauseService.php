<?php


namespace Tagcade\Service\Core\VideoDemandAdTag;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRule;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\Report\CalculateRatiosTrait;

class AutoPauseService implements AutoPauseServiceInterface
{
    use CalculateRatiosTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AutoPauseService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function autoPauseDemandAdTags(array $demandAdTags)
    {
        $count = 0;
        foreach ($demandAdTags as $demandAdTag) {
            if (!$demandAdTag instanceof LibraryVideoDemandAdTagInterface) {
                throw new InvalidArgumentException('expect LibraryVideoDemandAdTagInterface object');
            }

            $count += $this->autoPauseDemandAdTag($demandAdTag);
        }

        return $count;
    }


    protected function autoPauseDemandAdTag(LibraryVideoDemandAdTagInterface $demandAdTag)
    {
        $count = 0;
        $demandAdTags = $demandAdTag->getVideoDemandAdTags();
        $rules = $demandAdTag->getWaterfallPlacementRules();

        /** @var VideoDemandAdTagInterface $tag */
        foreach ($demandAdTags as $tag) {
            /** @var WaterfallPlacementRuleInterface $rule */
            foreach ($rules as $rule) {
                if ($this->validateDemandAdTagAgainstPlacementRule($tag, $rule) === false) {
                    $tag->setActive(VideoDemandAdTag::AUTO_PAUSED);
                    $this->em->merge($tag);
                    $count++;
                }
            }
        }

        $this->em->flush();

        return $count;
    }

    protected function validateDemandAdTagAgainstPlacementRule(VideoDemandAdTagInterface $demandAdTag, WaterfallPlacementRuleInterface $rule)
    {
        $libraryVideoDemandAdTag = $demandAdTag->getLibraryVideoDemandAdTag();
        $waterfallTag = $demandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag();

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