<?php


namespace Tagcade\Worker\Workers;


use stdClass;
use Tagcade\Entity\Core\WaterfallPlacementRule;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Repository\Core\WaterfallPlacementRuleRepositoryInterface;
use Tagcade\Service\Core\VideoDemandAdTag\DeployLibraryVideoDemandAdTagServiceInterface;

class DeployVideoDemandAdTagForNewPlacementRuleWorker
{
    /**
     * @var DeployLibraryVideoDemandAdTagServiceInterface
     */
    private $deployService;

    /**
     * @var WaterfallPlacementRuleRepositoryInterface
     */
    private $waterfallPlacementRuleRepository;

    /**
     * DeployVideoDemandAdTagForNewPlacementRuleWorker constructor.
     * @param $deployService
     * @param WaterfallPlacementRuleRepositoryInterface $waterfallPlacementRuleRepository
     */
    public function __construct($deployService, WaterfallPlacementRuleRepositoryInterface $waterfallPlacementRuleRepository)
    {
        $this->deployService = $deployService;
        $this->waterfallPlacementRuleRepository = $waterfallPlacementRuleRepository;
    }


    public function deployVideoDemandAdTagForNewPlacementRule(StdClass $param)
    {
        $ruleId = $param->ruleId;
        $placementRule = $this->waterfallPlacementRuleRepository->find($ruleId);

        if (!$placementRule instanceof WaterfallPlacementRuleInterface) {
            return;
        }

        if ($placementRule->getProfitType() === WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_MANUAL) {
            if (empty($placementRule->getWaterfalls())) {
                return;
            }

            $this->deployService->deployLibraryVideoDemandAdTagToWaterfalls(
                $placementRule->getLibraryVideoDemandAdTag(),
                $placementRule,
                $placementRule->getWaterfalls(),
                null,
                false,
                $placementRule->getPriority(),
                $placementRule->getRotationWeight(),
                $placementRule->isActive(),
                $placementRule->getPosition(),
                $placementRule->isShiftDown()
            );

            return;
        }

        $tags = $this->deployService->getValidVideoWaterfallTagsForPlacementRule($placementRule);
        $this->deployService->deployLibraryVideoDemandAdTagToWaterfalls(
            $placementRule->getLibraryVideoDemandAdTag(),
            $placementRule,
            $tags,
            null,
            false,
            $placementRule->getPriority(),
            $placementRule->getRotationWeight(),
            $placementRule->isActive(),
            $placementRule->getPosition(),
            $placementRule->isShiftDown()
        );
    }
}