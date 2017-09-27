<?php


namespace Tagcade\Behaviors;


use FOS\UserBundle\Model\User;
use Tagcade\Model\User\Role\PublisherInterface;

trait UserUtilTrait
{
    /**
     * @param PublisherInterface $entity
     * @return array
     */
    private function generatePublisherData(PublisherInterface $entity)
    {
        $entityArray = array();

        $entityArray['id'] = $entity->getId();
        $entityArray['firstName'] = $entity->getFirstName();
        $entityArray['lastName'] = $entity->getLastName();
        $entityArray['company'] = $entity->getCompany();
        $entityArray['phone'] = $entity->getPhone();
        $entityArray['city'] = $entity->getCity();
        $entityArray['state'] = $entity->getState();
        $entityArray['address'] = $entity->getAddress();
        $entityArray['postalCode'] = $entity->getPostalCode();
        $entityArray['country'] = $entity->getCountry();
        $entityArray['enabledModules'] = $entity->getEnabledModules();
        $entityArray['username'] = $entity->getUsername();
        $entityArray['password'] = $entity->getPassword();
        $entityArray['email'] = $entity->getEmail();
        $entityArray['enabled'] = $entity->isEnabled();
        $entityArray['roles'] = $entity->getRoles();
        $entityArray['masterAccount'] = ($entity->getMasterAccount() instanceof PublisherInterface) ? $entity->getMasterAccount()->getId() : null;
        $entityArray['emailSendAlert'] = $entity->getEmailSendAlert();

        $user = $entity->getUser();
        if ($user instanceof User) {
            $entityArray['salt'] = $user->getSalt();
        }
        return $entityArray;
    }
}