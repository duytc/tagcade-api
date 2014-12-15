<?php

namespace Tagcade\Bundle\AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\ModelInterface;

class UpdateCacheEvent extends Event
{

    const NAME = 'tagcade_app.event.update_cache';
    /**
     * @var ModelInterface
     */
    private $entity;

    function __construct(ModelInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return ModelInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }


}