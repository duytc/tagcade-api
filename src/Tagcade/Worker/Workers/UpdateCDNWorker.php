<?php

namespace Tagcade\Worker\Workers;


use stdClass;
use Tagcade\Service\Cdn\CDNUpdaterInterface;

class UpdateCDNWorker {

    /**
     * @var CDNUpdaterInterface
     */
    private $cdnUpdater;

    function __construct(CDNUpdaterInterface $cdnUpdater)
    {
        $this->cdnUpdater = $cdnUpdater;
    }

    public function updateCdnForAdSlot(StdClass $param)
    {
        $this->cdnUpdater->pushAdSlot($param->adSlotId);
    }


    public function updateCdnForRonSlot(StdClass $param)
    {
        $this->cdnUpdater->pushRonSlot($param->ronSlotId);
    }
} 