<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Worker\Manager;

class UpdateCacheWhenAdSlotAutoOptimizeChangeListener
{
    /** @var Manager */
    private $manager;

    private $changedAdSlots = [];

    /**
     * UpdateCacheWhenAdSlotAutoOptimizeChangeListener constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof BaseAdSlotInterface || !$args->hasChangedField('autoOptimize')) {
            return;
        }

        $this->changedAdSlots[] = $entity;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedAdSlots) < 1) {
            return;
        }

        $adSlots = $this->changedAdSlots;
        $this->changedAdSlots = [];
        
        foreach($adSlots as $adSlot) {
            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }
            $this->manager->updateCacheForAdSlot($adSlot);
        }
    }
}