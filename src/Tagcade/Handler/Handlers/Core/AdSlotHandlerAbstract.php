<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\AdSlot;
use Tagcade\Model\Core\AdSlotInterface;

abstract class AdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * clone AdSlot
     * @param AdSlotInterface $originAdSlot
     * @param string $newName
     */
    public function cloneAdSlot(AdSlotInterface $originAdSlot, $newName)
    {
        $newAdSlot = clone $originAdSlot;
        $newAdSlot->setId(null);
        $newAdSlot->setName($newName);

        $this->getDomainManager()->save($newAdSlot);

        //dispatch event
        $event = $this->createCloneAdSlotEventLog($originAdSlot, $newAdSlot, $newName);
        $this->dispatchEvent($event);
    }

    /**
     * @param AdSlotInterface $originAdSlot
     * @param AdSlotInterface $newAdSlot
     * @param string $newName
     * @return HandlerEventLog
     */
    private function createCloneAdSlotEventLog(AdSlotInterface $originAdSlot, AdSlotInterface $newAdSlot, $newName)
    {
        $event = new HandlerEventLog('POST', $newAdSlot);

        //add changedFields
        $event->addChangedFields('[clone id]', $originAdSlot->getId(), $newAdSlot->getId());
        $event->addChangedFields('[clone name]', $originAdSlot->getName(), $newName);

        //add affectedEntities
        $event->addAffectedEntityByObject($originAdSlot);

        return $event;
    }
}
