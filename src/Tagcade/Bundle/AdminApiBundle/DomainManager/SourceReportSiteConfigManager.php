<?php

namespace Tagcade\Bundle\AdminApiBundle\DomainManager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Bundle\AdminApiBundle\Entity\SourceReportSiteConfig;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Repository\SourceReportSiteConfigRepositoryInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportSiteConfigManager implements SourceReportSiteConfigManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, SourceReportSiteConfigRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SourceReportSiteConfigInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(SourceReportSiteConfigInterface $siteConfig)
    {
        $this->om->persist($siteConfig);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(SourceReportSiteConfigInterface $siteConfig)
    {
        $this->om->remove($siteConfig);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new \ReflectionClass($this->repository->getClassName());
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
     * save sourceReportConfig for emailConfig with sites
     *
     * @param SourceReportEmailConfigInterface $emailConfig
     * @param SiteInterface[] $sites
     * @throws InvalidArgumentException
     */
    public function saveSourceReportConfig(SourceReportEmailConfigInterface $emailConfig, array $sites)
    {
        if (null === $emailConfig || null === $sites || empty($sites)) {
            throw new InvalidArgumentException('expect sites and emails');
        }

        // update site config for existing emailConfig
        /**
         * @var ArrayCollection $siteConfigs
         */
        $siteConfigs = $emailConfig->getSourceReportSiteConfigs();

        $siteConfigs = null === $siteConfigs ? [] : $siteConfigs->toArray();
        /**
         * @var array $siteConfigs
         */
        $existedSites = array_map(function (SourceReportSiteConfigInterface $siteConfig) {
            return $siteConfig->getSite();
        }, $siteConfigs);

        foreach ($sites as $site) {
            if (!in_array($site, $existedSites)) {
                $sourceReportSiteConfig = new SourceReportSiteConfig();
                $sourceReportSiteConfig->setSourceReportEmailConfig($emailConfig);
                $sourceReportSiteConfig->setSite($site);

                //persist new
                $this->om->persist($sourceReportSiteConfig);
            }
        }

        $this->om->flush();
    }

    /**
     * Get source report site config for publisher and email
     *
     * @param PublisherInterface $publisher
     *
     * @param int $emailConfigId
     *
     * @return SourceReportSiteConfigInterface[]
     *
     */
    public function getSourceReportSiteConfigForPublisherAndEmailConfig(PublisherInterface $publisher, $emailConfigId)
    {
        return $this->repository->getSourceReportSiteConfigForPublisherAndEmailConfig($publisher, $emailConfigId);
    }
}