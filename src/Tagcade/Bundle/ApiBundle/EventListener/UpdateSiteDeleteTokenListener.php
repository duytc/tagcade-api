<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Tagcade\Model\Core\SiteInterface;

/**
 * This is a work around to have unique constraint working om publisher, domain with soft deleteable support.
 *
 * Class UpdateSiteDomainCanonicalListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateSiteDeleteTokenListener
{
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

        $deleteToken = uniqid('', true);
        $entity->setDeleteToken($deleteToken);
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

        $deleteToken = '0';
        $entity->setDeleteToken($deleteToken);
    }
} 