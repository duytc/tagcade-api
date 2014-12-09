<?php

namespace Tagcade\Bundle\AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Tagcade\Model\Core\AdSlotInterface;

class UpdateCacheEvent extends Event
{

    /**
     * @var AdSlotInterface
     */
    private $adSlot;

    function __construct(AdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
    }

    /**
     * @return AdSlotInterface
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }


}