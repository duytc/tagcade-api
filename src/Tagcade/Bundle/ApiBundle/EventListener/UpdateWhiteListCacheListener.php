<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\DomainListManagerInterface;
use Tagcade\Model\Core\WhiteListInterface;


class UpdateWhiteListCacheListener
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
        if (!$entity instanceof WhiteListInterface) {
            return;
        }

        $entity->setSuffixKey(strval($entity->getId()));
        $this->domainListManager->saveWhiteList($entity);
    }

    /**
     * handle event preUpdate to detect which site is been changing on 'domain', then update site_token to make sure site_token is unique for domain & publisher
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof WhiteListInterface || !$args->hasChangedField('domains')) {
            return;
        }

        $this->domainListManager->saveWhiteList($entity);
    }
}