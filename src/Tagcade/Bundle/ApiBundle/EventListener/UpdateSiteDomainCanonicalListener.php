<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\SiteInterface;

/**
 * This is a work around to have unique constraint working om publisher, domain with soft deleteable support.
 *
 * Class UpdateSiteDomainCanonicalListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateSiteDomainCanonicalListener {

    const DOMAIN_CANONICAL_PATTERN = '%d_%s_%s';

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof SiteInterface) {
            return;
        }

        if (($args->hasChangedField('domain') || $args->hasChangedField('publisher')) && !$args->hasChangedField('deletedAt')) {
            $domainCanonical = sprintf(self::DOMAIN_CANONICAL_PATTERN, $entity->getPublisher()->getId(), $entity->getDomain(), '0');
            $entity->setDomainCanonical($domainCanonical);
        }
    }

    /**
     * handle event prePersist to detect new ad tag is added, used for updating number of active|paused ad tags of ad network
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only listen Ad Tag instance
        if (!$entity instanceof SiteInterface) {
            return;
        }

        $domainCanonical = sprintf(self::DOMAIN_CANONICAL_PATTERN, $entity->getPublisher()->getId(), $entity->getDomain(), uniqid('', true));
        $entity->setDomainCanonical($domainCanonical);
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof SiteInterface) {
            return;
        }

        $domainCanonical = sprintf(self::DOMAIN_CANONICAL_PATTERN, $entity->getPublisher()->getId(), $entity->getDomain(), '0');
        $entity->setDomainCanonical($domainCanonical);
    }
} 