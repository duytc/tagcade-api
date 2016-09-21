<?php


namespace Tagcade\Service\Core\VideoDemandPartner;


use Tagcade\Domain\DTO\Core\WaterfallTagStatus;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

interface DemandPartnerServiceInterface
{
    /**
     * @param VideoDemandPartnerInterface $demandPartner
     * @return WaterfallTagStatus[]
     */
    public function getVideoWaterfallTagForVideoDemandPartner(VideoDemandPartnerInterface $demandPartner);
}