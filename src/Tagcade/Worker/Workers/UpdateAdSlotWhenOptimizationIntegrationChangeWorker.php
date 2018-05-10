<?php

namespace Tagcade\Worker\Workers;

use stdClass;
use Tagcade\Bundle\ApiBundle\EventListener\UpdateOptimizationIntegrationWhenAdSlotChangeListener;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;


class UpdateAdSlotWhenOptimizationIntegrationChangeWorker
{
    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    function __construct(AdSlotManagerInterface $adSlotManager)
    {
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * update cache for multiple site ids
     *
     * @param StdClass $params
     */
    public function synchronizeAdSlotWithOptimizationIntegration(stdClass $params)
    {
        $actions = $params->actions;
        $actions = is_array($actions) ? $actions : [$actions];

        foreach ($actions as $group) {
            $group = json_decode(json_encode($group), true);
            if (!array_key_exists(UpdateOptimizationIntegrationWhenAdSlotChangeListener::ACTION, $group) ||
                !array_key_exists(UpdateOptimizationIntegrationWhenAdSlotChangeListener::AD_SLOT, $group) ||
                !array_key_exists(UpdateOptimizationIntegrationWhenAdSlotChangeListener::OPTIMIZATION_INTEGRATION, $group)
            ) {
                continue;
            }

            $action = $group[UpdateOptimizationIntegrationWhenAdSlotChangeListener::ACTION];
            $adSlots = $group[UpdateOptimizationIntegrationWhenAdSlotChangeListener::AD_SLOT];
            $optimizationIntegration = $group[UpdateOptimizationIntegrationWhenAdSlotChangeListener::OPTIMIZATION_INTEGRATION];

            $this->syncAdSlotByDataFromUR($action, $adSlots, $optimizationIntegration);
        }
    }

    /**
     * @param $action
     * @param $adSlots
     * @param $optimizationIntegration
     */
    private function syncAdSlotByDataFromUR($action, $adSlots, $optimizationIntegration)
    {
        $adSlots = is_array($adSlots) ? $adSlots : [$adSlots];

        foreach ($adSlots as $adSlotId) {
            $adSlot = $this->adSlotManager->find($adSlotId);
            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }

            switch ($action) {
                case UpdateOptimizationIntegrationWhenAdSlotChangeListener::ACTION_ADD:
                    $adSlot->setOptimizationIntegration($optimizationIntegration);
                    break;
                case UpdateOptimizationIntegrationWhenAdSlotChangeListener::ACTION_REMOVE:
                    if ($adSlot->getOptimizationIntegration() == $optimizationIntegration) {
                        $adSlot->setOptimizationIntegration(null);
                    }
                    break;
            }

            $this->adSlotManager->save($adSlot);
        }
    }
}