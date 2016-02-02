<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\ChannelRepositoryInterface;

class ChannelManager implements ChannelManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, ChannelRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, ChannelInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $channel)
    {
        if (!$channel instanceof ChannelInterface) throw new InvalidArgumentException('expect ChannelInterface object');
        $this->om->persist($channel);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $channel)
    {
        if (!$channel instanceof ChannelInterface) throw new InvalidArgumentException('expect ChannelInterface object');
        $this->om->remove($channel);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getChannelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getChannelsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getChannelsIncludeSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null)
    {
        return $this->repository->getChannelsIncludeSitesUnreferencedToLibraryAdSlot($slotLibrary, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getChannelsHaveSiteForUser(UserRoleInterface $user, $limit = null, $offset = null)
    {
        return $this->repository->getChannelsHaveSiteForUser($user, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function deleteSiteForChannel(ChannelInterface $channel, $siteId)
    {
        $channelSites = $channel->getChannelSites();

        if ($channelSites instanceof Collection) {
            $channelSites = $channelSites->toArray();
        }

        //number of removed channels
        $removedCount = 0;

        foreach ($channelSites as $idx => $cs) {
            if ($cs->getSite()->getId() == $siteId) {
                //remove matched element
                $this->om->remove($cs);
                $removedCount++;
            }
        }

        //flush to db if has element removed
        if ($removedCount > 0) {
            $this->om->flush();
        }

        //return number of removed channels
        return $removedCount;
    }
}