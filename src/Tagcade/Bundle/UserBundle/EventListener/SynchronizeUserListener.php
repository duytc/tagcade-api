<?php

namespace Tagcade\Bundle\UserBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Worker\Manager;

class SynchronizeUserListener
{
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

        if ($entity->hasUnifiedReportModule()) {
            $entityArray = $this->entityPrepared($entity);
            $this->workerManager->synchronizeUser($entity->getId(), $entityArray);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PublisherInterface) {
            return;
        }

        if ($args->hasChangedField('enabled') || $args->hasChangedField('roles')) {
            $entityArray = $this->entityPrepared($entity);
            $this->workerManager->synchronizeUser($entity->getId(), $entityArray);
        }
    }

    private function entityPrepared (PublisherInterface $entity)
    {
        $entityArray = array();
        $entityArray['id'] = $entity->getId();
//        $entityArray['billingRate'] = $entity->getBillingRate();
//        $entityArray['billingConfigs'] = $entity->getBillingConfigs();
//        $entityArray['firstName'] = $entity->getFirstName();
//        $entityArray['lastName'] = $entity->getLastName();
        $entityArray['company'] = $entity->getCompany();
//        $entityArray['phone'] = $entity->getPhone();
//        $entityArray['city'] = $entity->getCity();
//        $entityArray['state'] = $entity->getState();
//        $entityArray['address'] = $entity->getAddress();
//        $entityArray['postalCode'] = $entity->getPostalCode();
//        $entityArray['country'] = $entity->getCountry();
//        $entityArray['settings'] = $entity->getSettings();
//        $entityArray['tagDomain'] = $entity->getTagDomain();
//        $entityArray['exchanges'] = $entity->getExchanges();
//        $entityArray['bidders'] = $entity->getBidders();
//        $entityArray['enabledModules'] = $entity->getEnabledModules();
        $entityArray['username'] = $entity->getUsername();
        $entityArray['password'] = $entity->getPassword();
        $entityArray['email'] = $entity->getEmail();
        $entityArray['enabled'] = $entity->isEnabled();
        $entityArray['roles'] = $entity->getRoles();

        return $entityArray;
    }
}