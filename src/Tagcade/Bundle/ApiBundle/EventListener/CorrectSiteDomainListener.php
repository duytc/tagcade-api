<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\StringUtilTrait;

class CorrectSiteDomainListener
{
    use StringUtilTrait;

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof SiteInterface) {
            return;
        }

        $this->correctSiteDomainIfNeeded($entity);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof SiteInterface || ($entity instanceof SiteInterface && !$args->hasChangedField('domain'))) {
            return;
        }

        $this->correctSiteDomainIfNeeded($entity);
    }

    /**
     * @param SiteInterface $site
     */
    private function correctSiteDomainIfNeeded(SiteInterface &$site)
    {
        $newDomain = $this->extractDomain($site->getDomain());

        if (strcmp($newDomain, $site->getDomain()) !== 0) {
            $site->setDomain($newDomain);
        }
    }
}