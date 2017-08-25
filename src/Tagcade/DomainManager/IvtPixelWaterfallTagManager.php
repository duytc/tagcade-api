<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\IvtPixelWaterfallTagRepositoryInterface;

class IvtPixelWaterfallTagManager implements IvtPixelWaterfallTagManagerInterface
{
    /** @var ObjectManager */
    protected $om;
    /** @var IvtPixelWaterfallTagRepositoryInterface */
    protected $repository;

    public function __construct(ObjectManager $om, IvtPixelWaterfallTagRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, IvtPixelWaterfallTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $ivtPixelVideoWaterfallTag)
    {
        if (!$ivtPixelVideoWaterfallTag instanceof IvtPixelWaterfallTagInterface) throw new InvalidArgumentException('expect IvtPixelVideoWaterfallTagInterface object');

        $this->om->persist($ivtPixelVideoWaterfallTag);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $ivtPixelVideoWaterfallTag)
    {
        if (!$ivtPixelVideoWaterfallTag instanceof IvtPixelWaterfallTagInterface) throw new InvalidArgumentException('expect IvtPixelVideoWaterfallTagInterface object');

        $this->om->remove($ivtPixelVideoWaterfallTag);
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
    public function getIvtPixelWaterfallTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getIvtPixelWaterfallTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelWaterfallTagsByIvtPixel(IvtPixelInterface $ivtPixel, $limit = null, $offset = null)
    {
        return $this->repository->getIvtPixelWaterfallTagsByIvtPixel($ivtPixel, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelWaterfallTagsByWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null)
    {
        return $this->repository->getIvtPixelWaterfallTagsByWaterfallTag($videoWaterfallTag, $limit, $offset);
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