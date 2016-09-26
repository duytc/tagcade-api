<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\VideoWaterfallTagRepositoryInterface;

class VideoWaterfallTagManager implements VideoWaterfallTagManagerInterface
{
    /** @var ObjectManager */
    protected $om;
    /** @var VideoWaterfallTagRepositoryInterface */
    protected $repository;

    public function __construct(ObjectManager $om, VideoWaterfallTagRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, VideoWaterfallTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $VideoWaterfallTag)
    {
        if (!$VideoWaterfallTag instanceof VideoWaterfallTagInterface) throw new InvalidArgumentException('expect VideoWaterfallTagInterface object');

        $this->om->persist($VideoWaterfallTag);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $VideoWaterfallTag)
    {
        if (!$VideoWaterfallTag instanceof VideoWaterfallTagInterface) throw new InvalidArgumentException('expect VideoWaterfallTagInterface object');

        $this->om->remove($VideoWaterfallTag);
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
    public function getVideoWaterfallTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getVideoWaterfallTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagsForVideoPublisher(VideoPublisherInterface $videoPublisher, $limit = null, $offset = null)
    {
        return $this->repository->getVideoWaterfallTagsForVideoPublisher($videoPublisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getWaterfallTagsNotLinkToLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, UserRoleInterface $user)
    {
        return $this->repository->getWaterfallTagsNotLinkToLibraryVideoDemandAdTag($libraryVideoDemandAdTag, $user);
    }

    public function getWaterfallTagsForVideoDemandPartner(VideoDemandPartnerInterface $demandPartner, $limit = null, $offset = null)
    {
        return $this->repository->getWaterfallTagsForVideoDemandPartner($demandPartner, $limit, $offset);
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