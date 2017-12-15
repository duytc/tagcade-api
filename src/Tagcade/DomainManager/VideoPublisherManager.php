<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\VideoPublisherRepositoryInterface;

class VideoPublisherManager implements VideoPublisherManagerInterface
{
    /** @var ObjectManager */
    protected $om;
    /** @var VideoPublisherRepositoryInterface */
    protected $repository;

    public function __construct(ObjectManager $om, VideoPublisherRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, VideoPublisherInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $videoPublisher)
    {
        if (!$videoPublisher instanceof VideoPublisherInterface) throw new InvalidArgumentException('expect VideoPublisherInterface object');

        $this->om->persist($videoPublisher);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $videoPublisher)
    {
        if (!$videoPublisher instanceof VideoPublisherInterface) throw new InvalidArgumentException('expect VideoPublisherInterface object');

        $this->om->remove($videoPublisher);
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
    public function getVideoPublishersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getVideoPublishersForPublisher($publisher, $limit, $offset);
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

    /**
     * @inheritdoc
     */
    public function findByNameAndPublisherId($name, $publisherId, $limit = null, $offset = null)
    {
        return $this->repository->findByNameAndPublisherId($name, $publisherId, $limit, $offset);
    }
}