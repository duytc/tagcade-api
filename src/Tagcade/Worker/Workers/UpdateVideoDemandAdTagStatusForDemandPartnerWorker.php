<?php

namespace Tagcade\Worker\Workers;

use Doctrine\ORM\EntityManagerInterface;
use StdClass;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\DomainManager\VideoDemandPartnerManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateVideoDemandAdTagStatusForDemandPartnerWorker
{
    protected $waterfallTagManager;
    protected $videoDemandPartnerManager;
    protected $videoDemandAdTagManager;
    protected $em;

    public function __construct(EntityManagerInterface $em, VideoDemandAdTagManagerInterface $videoDemandAdTagManager, VideoWaterfallTagManagerInterface $waterfallTagManager, VideoDemandPartnerManagerInterface $videoDemandPartnerManager)
    {
        $this->em = $em;
        $this->videoDemandAdTagManager = $videoDemandAdTagManager;
        $this->waterfallTagManager = $waterfallTagManager;
        $this->videoDemandPartnerManager = $videoDemandPartnerManager;
    }

    public function updateVideoDemandAdTagStatusForDemandPartner(StdClass $params)
    {
        /** @var VideoDemandPartnerInterface $demandPartner */
        $demandPartner = $this->videoDemandPartnerManager->find($params->videoDemandPartner);

        if (!$demandPartner instanceof VideoDemandPartnerInterface) {
            throw new InvalidArgumentException('That demandPartner does not exist');
        }

        $status = filter_var($params->status, FILTER_VALIDATE_INT);
        if (isset($params->waterfallTagId)) {
            $waterfallTag = $this->waterfallTagManager->find($params->waterfallTagId);
            if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
                throw new InvalidArgumentException('That waterfall tag does not exist');
            }

            $this->videoDemandAdTagManager->updateVideoDemandAdTagForDemandPartner($demandPartner, $status, $waterfallTag);
            return;
        }

        $this->videoDemandAdTagManager->updateVideoDemandAdTagForDemandPartner($demandPartner, $status);
    }
}