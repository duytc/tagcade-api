<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;

interface UnlinkServiceInterface
{
    /**
     * @param AdTagInterface $adTag
     * @return mixed
     */
    public function unlinkForAdTag(AdTagInterface $adTag);

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return mixed
     */
    public function unlinkForAdSlot(BaseAdSlotInterface $adSlot);
}