<?php

namespace Tagcade\Worker\Workers;

use stdClass;
use Tagcade\Bundle\ApiBundle\EventListener\UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;


class UpdateVideoWaterfallTagWhenOptimizationIntegrationChangeWorker
{
    /** @var VideoWaterfallTagManagerInterface */
    private $videoWaterfallTagManager;

    function __construct(VideoWaterfallTagManagerInterface $videoWaterfallTagManager)
    {
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
    }

    /**
     * update cache for multiple site ids
     *
     * @param StdClass $params
     */
    public function synchronizeVideoWaterfallTagWithOptimizationIntegration(stdClass $params)
    {
        $actions = $params->actions;
        $actions = is_array($actions) ? $actions : [$actions];

        foreach ($actions as $group) {
            $group = json_decode(json_encode($group), true);
            if (!array_key_exists(UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::ACTION, $group) ||
                !array_key_exists(UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::VIDEO_WATERFALL_TAG, $group) ||
                !array_key_exists(UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::OPTIMIZATION_INTEGRATION, $group)
            ) {
                continue;
            }

            $action = $group[UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::ACTION];
            $videoWaterfallTagIds = $group[UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::VIDEO_WATERFALL_TAG];
            $optimizationIntegrationId = $group[UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::OPTIMIZATION_INTEGRATION];

            $this->syncVideoWaterfallTagByDataFromUR($action, $videoWaterfallTagIds, $optimizationIntegrationId);
        }
    }

    /**
     * @param string $action
     * @param array|int $videoWaterfallTagIds
     * @param int $optimizationIntegrationId
     */
    private function syncVideoWaterfallTagByDataFromUR($action, $videoWaterfallTagIds, $optimizationIntegrationId)
    {
        $videoWaterfallTagIds = is_array($videoWaterfallTagIds) ? $videoWaterfallTagIds : [$videoWaterfallTagIds];

        foreach ($videoWaterfallTagIds as $videoWaterfallTagId) {
            $videoWaterfallTag = $this->videoWaterfallTagManager->find($videoWaterfallTagId);
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            switch ($action) {
                case UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::ACTION_ADD:
                    $videoWaterfallTag->setOptimizationIntegration($optimizationIntegrationId);
                    break;

                case UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener::ACTION_REMOVE:
                    if ($videoWaterfallTag->getOptimizationIntegration() == $optimizationIntegrationId) {
                        $videoWaterfallTag->setOptimizationIntegration(null);
                    }

                    break;
            }

            $this->videoWaterfallTagManager->save($videoWaterfallTag);
        }
    }
}