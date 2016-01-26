<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Behaviors\CreateSiteTokenTrait;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

/**
 * This is a work around to have unique constraint working om publisher, domain with soft deleteable support.
 *
 * Class UpdateSiteDomainCanonicalListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateSiteTokenListener
{
    use CreateSiteTokenTrait;

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

        // Set site unique for the case of auto create.
        $entity->setSiteToken($this->generateSiteToken($args));
    }

    /**
     * handle event preSoftDelete to update site_token (of site-is-being-deleted) to make sure we can create site with same domain again (site_token then same previous site_token before updating)
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only listen Site instance
        if (!$entity instanceof SiteInterface) {
            return;
        }

        $siteToken = uniqid(null, true);
        $entity->setSiteToken($siteToken);
    }

    /**
     * handle event preUpdate to detect which site is been changing on 'domain', then update site_token to make sure site_token is unique for domain & publisher
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof SiteInterface || !$args->hasChangedField('domain')) {
            return;
        }

        // generate new site_token separate from previous site_token value
        $entity->setSiteToken($this->generateSiteToken($args));
    }

    /**
     * generate site_token for site from Lifecycle Event Args
     * @param LifecycleEventArgs $args
     * @return string site_token value
     */
    private function generateSiteToken(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof SiteInterface) {
            return false;
        }

        // generate new site_token separate from previous site_token value
        /** @var SiteRepositoryInterface $siteRepository */
        $siteRepository = $args->getEntityManager()->getRepository(Site::class);

        $similarSites = $siteRepository->findBy(array('publisher'=> $entity->getPublisher(), 'domain'=>$entity->getDomain()));
        $hasSameExistingDomain = (empty($similarSites) || count($similarSites) < 1) ? false : true;

        return (!$hasSameExistingDomain || $entity->isAutoCreate()) ?  $this->createSiteHash($entity->getPublisherId(), $entity->getDomain()) : uniqid(null, true);
    }
}