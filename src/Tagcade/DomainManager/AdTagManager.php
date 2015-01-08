<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\SortableListener;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Exception\InvalidArgumentException;
use ReflectionClass;

class AdTagManager implements AdTagManagerInterface
{
    protected $em;
    protected $repository;

    public function __construct(EntityManagerInterface $em, AdTagRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(AdTagInterface $adTag)
    {
        $this->em->persist($adTag);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(AdTagInterface $adTag)
    {
        $this->em->remove($adTag);
        $this->em->flush();
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
    public function getAdTagsForAdSlot(AdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdSlot($adSlot, $limit, $offset);
    }

    public function getAdTagsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetwork($adNetwork, $limit, $offset);
    }

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSite($adNetwork, $site, $limit, $offset);
    }

    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSites($adNetwork, $sites, $limit, $offset);
    }
}