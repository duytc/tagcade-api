<?php

namespace Tagcade\Bundle\AdminApiBundle\DomainManager;


use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SourceReportSiteConfigManagerInterface
{
    public function supportsEntity($entity);

    public function save(SourceReportSiteConfigInterface $siteConfig);

    public function delete(SourceReportSiteConfigInterface $siteConfig);

    public function createNew();

    public function find($id);

    public function all($limit = null, $offset = null);

    /**
     * save sourceReportConfig for emailConfig with sites
     *
     * @param SourceReportEmailConfigInterface $emailConfig
     * @param SiteInterface[] $sites
     * @throws InvalidArgumentException
     */
    public function saveSourceReportConfig(SourceReportEmailConfigInterface $emailConfig, array $sites);

    /**
     * Get source report site config for publisher and emailConfig
     *
     * @param PublisherInterface $publisher
     *
     * @param int $emailConfigId
     *
     * @return SourceReportSiteConfigInterface[]
     *
     */
    public function getSourceReportSiteConfigForPublisherAndEmailConfig(PublisherInterface $publisher, $emailConfigId);
}