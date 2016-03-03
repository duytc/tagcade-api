<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\ExchangeInterface;
use Tagcade\Model\Core\PublisherExchangeInterface;
use Tagcade\Model\Core\SiteInterface;

/**
 * Handle events to update Exchanges of a site due to Exchanges of Publisher is changed
 *
 * Class UpdateSiteDomainCanonicalListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateSiteExchangesListener
{
    /** @var array|SiteInterface[] */
    private $changedSites = [];

    /**
     * handle event preUpdate to detect which sites need be changed when publisher is been changing on 'exchanges', then update exchanges of site
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        $allSites = [];
        /** @var SiteManagerInterface $siteManager */
        $siteManager = $args->getEntityManager()->getRepository(Site::class);
        if ($entity instanceof ExchangeInterface && !$args->hasChangedField('canonicalName')) {
            /** @var PublisherExchangeInterface $publisherExchange */
            foreach($entity->getPublisherExchanges() as $publisherExchange) {
                $sites = $siteManager->getSitesForPublisher($publisherExchange->getPublisher());
                $allSites = array_merge($sites, $sites);
            }
            // get all affected sites belong to Publisher
            // notice: we will update all sites which have configured exchanges, not depend on rtb true or false because sites may be changed rtb later!!!


            // TODO this must be updated in the next phase
            $this->updateExchangesForSites($allSites);
        }

        if ($entity instanceof PublisherExchangeInterface && $args->hasChangedField('exchange')) {
            $sites = $siteManager->getSitesForPublisher($entity->getPublisher());
            $allSites = array_merge($sites, $sites);
            $this->updateExchangesForSites($allSites);
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PublisherExchangeInterface) {
            return ;
        }

        /** @var SiteManagerInterface $siteManager */
        $siteManager = $args->getEntityManager()->getRepository(Site::class);
        $sites = $siteManager->getSitesForPublisher($entity->getPublisher());

        $this->updateExchangesForSites($sites);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PublisherExchangeInterface) {
            return ;
        }

        /** @var SiteManagerInterface $siteManager */
        $siteManager = $args->getEntityManager()->getRepository(Site::class);
        $sites = $siteManager->getSitesForPublisher($entity->getPublisher());

        $this->updateExchangesForSites($sites);
    }

    /**
     * handle event postFlush to update exchanges of site to make sure not over range as in publisher
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedSites) < 1) {
            return;
        }

        // flush all changed sites
        $em = $args->getEntityManager();

        foreach ($this->changedSites as $site) {
            $em->merge($site);
        }

        // reset before flush
        $this->changedSites = [];

        $em->flush();
    }

    /**
     * update Exchanges For Sites Due To Publisher
     *
     * @param SiteInterface[] $sites
     * @return array|SiteInterface[]
     */
    private function updateExchangesForSites(array $sites)
    {
        foreach ($sites as $site) {
            /* // temporarily disable edit exchanges by manual, will be inherited from publisher
            if (!$site instanceof SiteInterface || count($site->getExchanges()) < 1) {
                continue;
            }

            $newExchanges = array_filter($site->getExchanges(), function ($exchange) use ($newSupportedExchanges) {
                return in_array($exchange, $newSupportedExchanges);
            });
            /* */

            // >> exchanges of site will be inherited from publisher...
            if (!$site instanceof SiteInterface) {
                continue;
            }

            $newExchanges = $site->getPublisher()->getExchanges();
            // << end

            $site->setExchanges($newExchanges);

            $this->changedSites[] = $site;
        }
    }
}