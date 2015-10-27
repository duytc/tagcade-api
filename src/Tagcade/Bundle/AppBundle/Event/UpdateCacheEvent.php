<?php

namespace Tagcade\Bundle\AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\ModelInterface;

class UpdateCacheEvent extends Event
{

    const NAME = 'tagcade_app.event.update_cache';

    private $entities = [];

    function __construct($entities = null)
    {
        if (null !== $entities) {

            if (!is_array($entities)) {
                $entities = array($entities);
            }

            foreach($entities as $entity) {
                if ($entity instanceof AdNetworkInterface || $entity instanceof BaseAdSlotInterface || $entity instanceof RonAdSlotInterface) {
                    $this->addEntity($entity);
                }
            }
        }
    }

    public function addEntity(ModelInterface $entity)
    {
        if (!in_array($entity, $this->entities)) {
            $this->entities[] = $entity;
        }

        return $this;
    }

    public function getEntities()
    {
        return $this->entities;
    }
}