<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\VideoWaterfallTagItemRepositoryInterface;

class VideoWaterfallTagItemManager implements VideoWaterfallTagItemManagerInterface
{
    /** @var ObjectManager */
    protected $om;
    /** @var VideoWaterfallTagItemRepositoryInterface */
    protected $repository;

    public function __construct(ObjectManager $om, VideoWaterfallTagItemRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, VideoWaterfallTagItemInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $videoWaterfallTagItem)
    {
        if (!$videoWaterfallTagItem instanceof VideoWaterfallTagItemInterface) throw new InvalidArgumentException('expect VideoWaterfallTagItemInterface object');

        $this->om->persist($videoWaterfallTagItem);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $videoWaterfallTagItem)
    {
        if (!$videoWaterfallTagItem instanceof VideoWaterfallTagItemInterface) throw new InvalidArgumentException('expect VideoWaterfallTagItemInterface object');

        $this->om->remove($videoWaterfallTagItem);
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
    public function getVideoWaterfallTagItemsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getVideoWaterfallTagItemsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagItemsForAdTag(VideoWaterfallTag $videoWaterfallTag, $limit = null, $offset = null)
    {
        return $this->repository->getVideoWaterfallTagItemsForAdTag($videoWaterfallTag, $limit, $offset);
    }

    /**
     * @param ModelInterface $site
     * @return mixed|void
     */
    public function persists(ModelInterface $site)
    {
        $this->om->persist($site);
    }

    /**
     * Flush message to data base
     */
    public function flush()
    {
        $this->om->flush();
    }
}