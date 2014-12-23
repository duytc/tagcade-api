<?php

namespace Tagcade\Service\Core\AdNetwork;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;

class AdNetworkService implements AdNetworkServiceInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function pauseAdNetwork(AdNetworkInterface $adNetwork)
    {
        $adNetwork->setActive(false);

        $this->em->flush();
    }

    public function updateActiveStateBySingleSiteForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site, $active = false)
    {

        foreach ($adNetwork->getAdTags() as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            if ($adTag->getAdSlot()->getSite() == $site && $active != $adTag->isActive()) {
                $adTag->setActive($active);
            }
        }

        $this->em->flush();
    }
}