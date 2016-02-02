<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class ChannelRepository extends EntityRepository implements ChannelRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getChannelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getChannelsForPublisherQuery($publisher, $limit, $offset);
        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getChannelsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('cl')
            ->where('cl.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getChannelsIncludeSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null)
    {
        $referencedChannels = [];
        $referencedSites = [];

        $referencedAdSlots = $slotLibrary->getAdSlots()->toArray();
        array_walk($referencedAdSlots,
            function (BaseAdSlotInterface $adSlot) use (&$referencedChannels, &$referencedSites) {
                $referencedChannels = array_merge($referencedChannels, $adSlot->getSite()->getChannels());
                $referencedSites[] = $adSlot->getSite();
            }
        );

        $referencedChannels = array_unique($referencedChannels);

        //filter all channels return channels which have all ad slots already referenced to library
        $referencedChannels = array_filter($referencedChannels,
            function($refChannel) use ($referencedSites) {
                /** @var ChannelInterface $refChannel */
                foreach($refChannel->getSites() as $refSite) {
                    if(!in_array($refSite, $referencedSites)) {
                        //still contains site not referenced to library -> skip this channel
                        return false;
                    }
                }
                return true;
            }
        );

        $qb = $this->createQueryBuilder('cn')
            ->where('cn.publisher = :publisher_id')
            ->setParameter('publisher_id', $slotLibrary->getPublisherId());

        if (count($referencedChannels) > 0) {
            $qb->andWhere('cn NOT IN (:referencedChannels)')
                ->setParameter('referencedChannels', $referencedChannels);
        }

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        $channels = $qb->getQuery()->getResult();
        $channels = array_filter($channels,
            function ($channel) {
                /** @var ChannelInterface $channel */
                return (count($channel->getSites()) > 0) ? true : false;
            }
        );

        return array_values($channels);
    }

    /**
     * @inheritdoc
     */
    public function getChannelsHaveSiteForUser(UserRoleInterface $user, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('cl');

        if ($user instanceof PublisherInterface) {
            // override prev $qb
            $qb = $this->getChannelsForPublisherQuery($user);
        }

        $qb
            ->join('cl.channelSites', 'cs');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}