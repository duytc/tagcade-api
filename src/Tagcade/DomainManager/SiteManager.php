<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;
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
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SiteInterface::class);
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

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);
    }

    public function getSitesThatHaveSourceReportConfigForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true)
    {
        return $this->repository->getSitesThatHastConfigSourceReportForPublisher($publisher, $hasSourceReportConfig);
    }

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher, $enableSourceReport = true) {
        return $this->repository->getSitesThatEnableSourceReportForPublisher($publisher, $enableSourceReport);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesThatEnableSourceReport($enableSourceReport = true) {
        return $this->repository->getAllSitesThatEnableSourceReport($enableSourceReport);
    }
}