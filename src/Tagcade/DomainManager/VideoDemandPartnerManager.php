<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\VideoDemandPartnerRepositoryInterface;

class VideoDemandPartnerManager implements VideoDemandPartnerManagerInterface
{
    /** @var ObjectManager */
    protected $om;
    /** @var VideoDemandPartnerRepositoryInterface */
    protected $repository;

    public function __construct(ObjectManager $om, VideoDemandPartnerRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, VideoDemandPartnerInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $videoDemandPartner)
    {
        if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) throw new InvalidArgumentException('expect VideoDemandPartnerInterface object');

        $this->om->persist($videoDemandPartner);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $videoDemandPartner)
    {
        if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) throw new InvalidArgumentException('expect VideoDemandPartnerInterface object');

        $this->om->remove($videoDemandPartner);
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
    public function getVideoDemandPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandPartnersForPublisher($publisher, $limit, $offset);
    }

    public function getVideoDemandPartnerForPublisherByCanonicalName(PublisherInterface $publisher, $canonicalName)
    {
        return $this->repository->getVideoDemandPartnerForPublisherByCanonicalName($publisher, $canonicalName);
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForVideoDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandAdTagsForVideoDemandPartner($videoDemandPartner, $limit, $offset);
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