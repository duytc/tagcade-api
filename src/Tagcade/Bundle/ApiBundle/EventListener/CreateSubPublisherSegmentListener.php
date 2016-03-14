<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Tagcade\Entity\Core\Segment;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class CreateSubPublisherSegmentListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof SubPublisherInterface) {
            return;
        }

        $em = $args->getEntityManager();

        $segment = new Segment();
        $segment->setPublisher($entity->getPublisher());
        $segment->setSubPublisher($entity);
        /**
         * @var UserEntityInterface $entity
         */
        $segment->setName($entity->getUsername());

        $em->persist($segment);
        $em->flush();
    }

} 