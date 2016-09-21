<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\DomainListManagerInterface;
use Tagcade\Model\Core\BlacklistInterface;


class UpdateBlacklistCacheListener
{
    /**
     * @var DomainListManagerInterface
     */
    private $domainListManager;

    /**
     * UpdateBlacklistCacheListener constructor.
     * @param DomainListManagerInterface $domainListManager
     */
    public function __construct(DomainListManagerInterface $domainListManager)
    {
        $this->domainListManager = $domainListManager;
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof BlacklistInterface) {
            return;
        }

        $entity->setSuffixKey(strval($entity->getId()));
        $this->domainListManager->saveBlacklist($entity);
    }

    /**
     * handle event preUpdate to detect which site is been changing on 'domain', then update site_token to make sure site_token is unique for domain & publisher
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof BlacklistInterface || !$args->hasChangedField('domains')) {
            return;
        }

        $this->domainListManager->saveBlacklist($entity);
    }
}