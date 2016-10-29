<?php


namespace Tagcade\Service\Core\VideoDemandAdTag;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Domain\DTO\Core\WaterfallTagsPlacementRule;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Entity\Core\WaterfallPlacementRule;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\LibraryVideoDemandAdTag;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Repository\Core\VideoWaterfallTagItemRepositoryInterface;
use Tagcade\Repository\Core\VideoWaterfallTagRepositoryInterface;

class DeployLibraryVideoDemandAdTagService implements DeployLibraryVideoDemandAdTagServiceInterface
{
    use CalculateRatiosTrait;
    /**
     * @var VideoWaterfallTagItemRepositoryInterface
     */
    private $waterfallTagItemRepository;

    /**
     * @var VideoWaterfallTagRepositoryInterface
     */
    private $waterfallTagRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DeployLibraryVideoDemandAdTagService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->waterfallTagItemRepository = $this->em->getRepository(VideoWaterfallTagItem::class);
        $this->waterfallTagRepository = $this->em->getRepository(VideoWaterfallTag::class);
    }

    /**
     * @inheritdoc
     */
    public function deployLibraryVideoDemandAdTagToWaterfalls(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $rule, array $videoWaterfallTags, $targeting = null, $targetingOverride = false, $priority = null, $rotationWeight = null, $active = true, $position = null, $shiftDown = false)
    {
        $availableWaterfalls = $this->waterfallTagRepository->getWaterfallTagsNotLinkToLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
        $availableWaterfalls = array_map(function(VideoWaterfallTagInterface $waterfall) {
            return $waterfall->getId();
        }, $availableWaterfalls);

        foreach ($videoWaterfallTags as $index => $videoWaterfallTagId) {
            $waterfallTag = $this->waterfallTagRepository->find($videoWaterfallTagId);

            if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
                throw new InvalidArgumentException(sprintf('video waterfall tag %d does not exist', $videoWaterfallTagId));
            }

            if (!in_array($videoWaterfallTagId, $availableWaterfalls)) {
                unset($videoWaterfallTags[$index]);
            }
        }

        if (!$rule instanceof WaterfallPlacementRuleInterface) {
            $rule = new WaterfallPlacementRule();
            $rule->setProfitType(WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_MANUAL)
                ->setPublishers([]);
            ;
        }

        $this->em->beginTransaction();
        try {
            foreach ($videoWaterfallTags as $videoWaterfallTag) {
                $demandAdTag = new VideoDemandAdTag();
                $demandAdTag
                    ->setLibraryVideoDemandAdTag($libraryVideoDemandAdTag)
                    ->setTargetingOverride($targetingOverride)
                    ->setWaterfallPlacementRule($rule)
                    ->setActive($active)
                ;

                if ($targetingOverride === true) {
                    $demandAdTag->setTargeting($targeting);
                } else {
                    $demandAdTag->setTargeting($libraryVideoDemandAdTag->getTargeting());
                }

                if (is_int($priority)) {
                    $demandAdTag->setPriority($priority);
                }

                if (is_int($rotationWeight)) {
                    $demandAdTag->setRotationWeight($rotationWeight);
                }

                $tag = $this->waterfallTagRepository->find($videoWaterfallTag);
                if ($tag instanceof VideoWaterfallTagInterface) {
                    $tagTargeting = $demandAdTag->getTargeting();
                    $tagPlatform = array_key_exists(VideoDemandAdTag::TARGETING_KEY_PLATFORM, $tagTargeting) ? $tagTargeting[VideoDemandAdTag::TARGETING_KEY_PLATFORM] : [];
                    $waterfallPlatform = $tag->getPlatform();
                    if (count(array_diff($tagPlatform, $waterfallPlatform)) > 0) {
                        continue;
                    }
                    $this->deployToSingleWaterfall($demandAdTag, $tag, $position, $shiftDown);
                }
            }
            $this->em->commit();
        } catch (InvalidArgumentException $ex) {
            $this->em->rollback();
            throw $ex;
        }
    }

    public function getValidVideoWaterfallTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $demandAdTag)
    {
        $waterfallTags = [];
        $placementRules = $demandAdTag->getWaterfallPlacementRules();

        /** @var WaterfallPlacementRuleInterface $placementRule */
        foreach($placementRules as $placementRule) {
            $tags = $this->waterfallTagRepository->getWaterfallTagHaveBuyPriceLowerThanAndBelongsToListPublishers(
                $demandAdTag->getVideoDemandPartner()->getPublisher(),
                $placementRule->getPublishers(),
                $this->calculateMinimumBuyPriceForPlacementRule($placementRule)
            );

            if (empty($tags)) {
                continue;
            }

            $waterfallTags[] = new WaterfallTagsPlacementRule($placementRule, $tags);
        }

        return $waterfallTags;
    }

    public function getValidVideoWaterfallTagsForPlacementRule(WaterfallPlacementRuleInterface $placementRule)
    {
        return $this->waterfallTagRepository->getWaterfallTagHaveBuyPriceLowerThanAndBelongsToListPublishers(
            $placementRule->getLibraryVideoDemandAdTag()->getVideoDemandPartner()->getPublisher(),
            $placementRule->getPublishers(),
            $this->calculateMinimumBuyPriceForPlacementRule($placementRule)
        );
    }

    /**
     * @param VideoDemandAdTagInterface $demandAdTag
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param null $position
     * @param bool $shiftDown
     */
    protected function deployToSingleWaterfall(VideoDemandAdTagInterface $demandAdTag, VideoWaterfallTagInterface $videoWaterfallTag, $position = null, $shiftDown = false)
    {
        $waterfallPlatform = $videoWaterfallTag->getPlatform();
        $demandAdTagPlatform = $demandAdTag->getTargeting()[LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM];

        if (count($waterfallPlatform) > 0 && count($demandAdTagPlatform) > 0) {
            $diff = array_diff($demandAdTagPlatform, $waterfallPlatform);

            if (count($diff) > 0) {
                throw new InvalidArgumentException(sprintf('waterfall "%s" does not have platform "%s"', $videoWaterfallTag->getName(), implode(',', $diff)));
            }
        }

        if (!is_int($position)) {
            $this->assignVideoDemandAdTagToNewWaterfallTagItem($demandAdTag, $videoWaterfallTag);
            return;
        }

        if ($shiftDown === true) {
            $this->assignVideoDemandAdTagToNewWaterfallTagItemWithShiftDown($demandAdTag, $videoWaterfallTag, $position);
            return;
        }

        $waterfallTagItem = $this->waterfallTagItemRepository->getWaterfallTagItemWithPositionInWaterfallTag($videoWaterfallTag, $position);
        if (!$waterfallTagItem instanceof VideoWaterfallTagItemInterface) {
            $this->assignVideoDemandAdTagToNewWaterfallTagItem($demandAdTag, $videoWaterfallTag);
            return;
        }

        $demandAdTag->setVideoWaterfallTagItem($waterfallTagItem);

        $this->em->persist($demandAdTag);
        $this->em->flush();
    }

    /**
     * @param VideoDemandAdTagInterface $demandAdTag
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     */
    protected function assignVideoDemandAdTagToNewWaterfallTagItem(VideoDemandAdTagInterface $demandAdTag, VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $waterfallTagItem = new VideoWaterfallTagItem();
        $maxPosition = $this->waterfallTagItemRepository->getMaxPositionInWaterfallTag($videoWaterfallTag);
        $waterfallTagItem
            ->setStrategy(VideoWaterfallTagItem::STRATEGY_LINEAR)
            ->setVideoWaterfallTag($videoWaterfallTag)
            ->addVideoDemandAdTag($demandAdTag)
            ->setPosition($maxPosition !== null ? (int)$maxPosition + 1 : 1);

        $demandAdTag->setVideoWaterfallTagItem($waterfallTagItem);

        $this->em->merge($videoWaterfallTag);
        $this->em->persist($waterfallTagItem);
        $this->em->flush();
    }

    /**
     * @param VideoDemandAdTagInterface $demandAdTag
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param $position
     */
    protected function assignVideoDemandAdTagToNewWaterfallTagItemWithShiftDown(VideoDemandAdTagInterface $demandAdTag, VideoWaterfallTagInterface $videoWaterfallTag, $position)
    {
        $waterfallTagItemRepository = $this->em->getRepository(VideoWaterfallTagItem::class);
        $waterfallTagItems = $waterfallTagItemRepository->getVideoWaterfallTagItemsForAdTag($videoWaterfallTag);

        /** @var VideoWaterfallTagItemInterface $waterfallTagItem */
        foreach ($waterfallTagItems as $waterfallTagItem) {
            $currentPosition = $waterfallTagItem->getPosition();
            if ($currentPosition >= $position) {
                $waterfallTagItem->setPosition($currentPosition + 1);
                $this->em->merge($waterfallTagItem);
            }
        }

        $newWaterfallTagItem = new VideoWaterfallTagItem();
        $newWaterfallTagItem
            ->setStrategy(VideoWaterfallTagItem::STRATEGY_LINEAR)
            ->setVideoWaterfallTag($videoWaterfallTag)
            ->addVideoDemandAdTag($demandAdTag)
            ->setPosition($position);

        $demandAdTag->setVideoWaterfallTagItem($newWaterfallTagItem);

        $this->em->persist($newWaterfallTagItem);
        $this->em->flush();
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