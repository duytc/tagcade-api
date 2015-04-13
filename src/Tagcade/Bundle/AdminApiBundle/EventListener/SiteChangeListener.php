<?php

namespace Tagcade\Bundle\AdminApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportEmailConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportSiteConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\Event\NewSourceConfigEvent;
use Tagcade\Bundle\AdminApiBundle\Event\UpdateSourceConfigEvent;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\Core\SiteInterface;

class SiteChangeListener
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $enableSourceReportUpdated = false;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args -> getEntity();

        if($entity instanceof SiteInterface) {
            $this->enableSourceReportUpdated = $args->hasChangedField('enableSourceReport');
        }
    }
    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof SiteInterface && true === $entity->getEnableSourceReport()){
            $defaultEMail = $entity->getPublisher()->getEmail();
            $this->eventDispatcher->dispatch(NewSourceConfigEvent::NAME, new NewSourceConfigEvent(array($defaultEMail), array($entity)));
        }

    }

    /**
     * handle event postUpdate one site, this auto remove SourceReportSiteConfigs if site's enableSourceReport change from true to false.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof SiteInterface && $this->enableSourceReportUpdated){
            //dispatch
            $this->eventDispatcher->dispatch(UpdateSourceConfigEvent::NAME, new UpdateSourceConfigEvent($entity));
        }
    }
}