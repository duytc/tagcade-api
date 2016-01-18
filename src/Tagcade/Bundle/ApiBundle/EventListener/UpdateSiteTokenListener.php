<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
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

        /**
         * @var SiteRepositoryInterface $siteRepository
         */
        $siteRepository = $args->getEntityManager()->getRepository(Site::class);

        $similarSites = $siteRepository->findBy(array('publisher'=> $entity->getPublisher(), 'domain'=>$entity->getDomain()));
        $hasSameExistingDomain = (empty($similarSites) || count($similarSites) < 1) ? false : true;


        // Set site unique for the case of auto create.
        $siteToken = (!$hasSameExistingDomain || $entity->isAutoCreate()) ?  $this->createSiteHash($entity->getPublisherId(), $entity->getDomain()) : uniqid(null, true);
        $entity->setSiteToken($siteToken);
    }
} 