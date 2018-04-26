<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Worker\Manager;

class UpdateAdSlotCacheWhenSiteAutoOptimizeChangeListener
{
    /** @var Manager */
    private $manager;

    /**
     * UpdateAdSlotCacheWhenSiteAutoOptimizeChangeListener constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof SiteInterface || !$args->hasChangedField('autoOptimize')) {
            return;
        }

        // generate new site_token separate from previous site_token value
        $adSlots = $entity->getAllAdSlots();

        foreach ($adSlots as $adSlot) {
            $this->manager->updateCacheForAdSlot($adSlot);
        }
    }
}