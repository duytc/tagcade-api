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

    public function pauseAdNetworkBySites(array $sites)
    {
        foreach ($sites as $site) {

            if (!$site instanceof SiteInterface) {
                throw new InvalidArgumentException('Expect SiteInterface');
            }

            foreach ($site->getAdSlots() as $adSlot) {
                /**
                 * @var AdSlotInterface $adSlot
                 */
                foreach ($adSlot->getAdTags() as $adTag) {
                    /**
                     * @var AdTagInterface $adTag
                     */
                    if (true === $adTag->isActive()) {
                        $adTag->setActive(false);
                    }
                }
            }
        }

        $this->em->flush();
    }
}