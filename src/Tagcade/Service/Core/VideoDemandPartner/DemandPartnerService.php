<?php


namespace Tagcade\Service\Core\VideoDemandPartner;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Domain\DTO\Core\WaterfallTagStatus;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

class DemandPartnerService implements DemandPartnerServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getVideoWaterfallTagForVideoDemandPartner(VideoDemandPartnerInterface $demandPartner)
    {
        $waterfallTagStatus = [];
        $repository = $this->em->getRepository(VideoWaterfallTag::class);
        $waterfallTags = $repository->getWaterfallTagsForVideoDemandPartner($demandPartner);

        foreach($waterfallTags as $waterfallTag) {
            $waterfallTagStatus[] = new WaterfallTagStatus($waterfallTag, $demandPartner);
        }

        return $waterfallTagStatus;
    }
}