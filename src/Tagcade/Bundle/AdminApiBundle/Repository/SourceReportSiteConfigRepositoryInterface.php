<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportSiteConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SourceReportSiteConfigRepositoryInterface extends ObjectRepository
{

    /**
     * Get source report config for publisher
     *
     * @param PublisherInterface $publisher
     *
     * @return SourceReportEmailConfigInterface[]
     *
     */
    public function getSourceReportSiteConfigForPublisher(PublisherInterface $publisher);

    /**
     * Get source report site config for publisher and emailConfig
     *
     * @param PublisherInterface $publisher
     *
     * @param SourceReportEmailConfigInterface $emailConfig
     *
     * @return SourceReportSiteConfigInterface[]
     *
     */
    public function getSourceReportSiteConfigForPublisherAndEmailConfig(PublisherInterface $publisher, SourceReportEmailConfigInterface $emailConfig);

    /**
     * Get source report site config for emailConfig
     *
     * @param SourceReportEmailConfigInterface $emailConfig
     *
     * @return SourceReportSiteConfigInterface[]
     *
     */
    public function getSourceReportSiteConfigForEmailConfig(SourceReportEmailConfigInterface $emailConfig);
} 