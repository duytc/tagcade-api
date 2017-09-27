<?php

namespace Tagcade\Bundle\UserBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Behaviors\UserUtilTrait;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Worker\Manager;

class UpdatePublisherListener
{
    use UserUtilTrait;

    /**
     * @var Manager
     */
    private $workerManager;

    function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof PublisherInterface) {
            return;
        }

        if ($entity instanceof SubPublisherInterface) {
            return;
        }

        if ($entity->isEnabled() && $entity->hasUnifiedReportModule()) {
            $entityArray = $this->generatePublisherData($entity);
            $this->workerManager->synchronizeUser($entityArray);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PublisherInterface || $entity instanceof SubPublisherInterface) {
            return;
        }

        if ($args->hasChangedField('enabled')
            || $args->hasChangedField('roles')
            || $args->hasChangedField('username')
            || $args->hasChangedField('password')
            || $args->hasChangedField('masterAccount')
            || $args->hasChangedField('emailSendAlert')
        ) {
            $entityArray = $this->generatePublisherData($entity);
            $this->workerManager->synchronizeUser($entityArray);
        }
    }
}