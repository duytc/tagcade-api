<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class BillingConfigurationRepository extends EntityRepository implements BillingConfigurationRepositoryInterface
{
    public function getAllConfigurationForPublisher(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('r')
            ->where('r.publisher = :publisher')
            ->setParameter('publisher', $publisher)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getConfigurationForModule(PublisherInterface $publisher, $module)
    {
        $billingConfiguration = $this->createQueryBuilder('r')
            ->where('r.publisher = :publisher')
            ->andWhere('r.module = :module')
            ->setParameter('publisher', $publisher)
            ->setParameter('module', $module)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (!$billingConfiguration instanceof BillingConfigurationInterface && $module == User::MODULE_DISPLAY) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }

        return $billingConfiguration;
    }
}