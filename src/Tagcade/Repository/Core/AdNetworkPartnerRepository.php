<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class AdNetworkPartnerRepository extends EntityRepository implements AdNetworkPartnerRepositoryInterface
{
    /**
     * @param int $publisherId
     * @return array
     */
    public function findByPublisher($publisherId)
    {
        $qb = $this->getPartnerForPublisherQuery($publisherId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserRoleInterface $user
     * @return array
     */

    public function findByUserRole(UserRoleInterface $user)
    {
        return $user instanceof AdminInterface ? $this->findAll() : $this->findByPublisher($user->getId());
    }

    /**
     * find Unused PartnersForPublisher
     *
     * @param UserRoleInterface $publisher
     * @return array
     */
    public function findUnusedPartnersForPublisher(UserRoleInterface $publisher)
    {
        if ($publisher instanceof AdminInterface) {
            return $this->findAll();
        }

        $usedPartners = $this->findByPublisher($publisher->getId());

        $allPartners = $this->findAll();

        $unusedPartners = [];

        foreach ($allPartners as $partner) {
            $found = false;
            foreach ($usedPartners as $used) {
                if ($partner->getId() == $used->getId()) {
                    $found = true;
                }
            }

            if ($found == false) {
                $unusedPartners[] = $partner;
            }
        }
        return $unusedPartners;
    }

    /**
     * @inheritdoc
     */
    protected function getPartnerForPublisherQuery($publisherId, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('np')
            ->join('np.publisherPartners', 'pubPartner')
            ->join('pubPartner.publisher', 'pub')
            ->where('pub.id = :publisher_id')
            ->setParameter('publisher_id', $publisherId, Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function findByCanonicalName($name)
    {
        return $this->createQueryBuilder('r')
            ->where('r.nameCanonical = :name')
            ->setParameter('name', $name)
            ->getQuery()->getOneOrNullResult();
    }
}