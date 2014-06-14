<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Model\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\SiteRepositoryInterface;
use ReflectionClass;

class SiteManager implements SiteManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, SiteRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function save(SiteInterface $site)
    {
        $this->om->persist($site);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(SiteInterface $site)
    {
        $this->om->remove($site);
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
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getSitesForPublisher($publisher, $limit, $offset);
    }
}