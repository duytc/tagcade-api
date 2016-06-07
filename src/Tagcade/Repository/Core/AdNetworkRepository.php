<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AdNetworkRepository extends EntityRepository implements AdNetworkRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdNetworksForPublisherQuery($publisher, $limit, $offset)
            ->addOrderBy('n.name', 'asc')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getAdNetworksForPublisherAndPartner(PublisherInterface $publisher, AdNetworkPartnerInterface $partner, $limit = null, $offset = null)
    {
        $qb = $this->getAdNetworksForPublisherQuery($publisher, $limit, $offset)
            ->andWhere('n.networkPartner = :partner_id')
            ->setParameter('partner_id', $partner->getId(), Type::INTEGER)
            ->addOrderBy('n.name', 'asc')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getAdNetworksThatHavePartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdNetworksForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere($qb->expr()->isNotNull('n.networkPartner'));

        return $qb->getQuery()->getResult();
    }

    public function getAdNetworksThatHavePartnerForSubPublisher(SubPublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->join('n.libraryAdTags', 'lt')
            ->join('lt.adTags', 't')
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st')
            ->where('st.subPublisher = :sub_publisher_id')
            ->setParameter('sub_publisher_id', $publisher->getId(), Type::INTEGER)
            ->andWhere($qb->expr()->isNotNull('n.networkPartner'))
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function allHasCap($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('n');
            $qb->where($qb->expr()->gt('n.networkOpportunityCap', 0))
                ->orWhere($qb->expr()->gt('n.impressionCap', 0))
        ;
        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworksForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $publisherId = ($publisher instanceof SubPublisherInterface) ? $publisher->getPublisher()->getId() : $publisher->getId();

        $qb = $this->createQueryBuilder('n')
            ->where('n.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisherId, Type::INTEGER)
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getPartnerConfigurationForAllPublishers($partnerCName, $withUnifiedReportModuleEnabled = true)
    {
        $result = $this->createQueryBuilder('r')
            ->join('r.publisher', 'p')
            ->join('r.networkPartner', 'np')
            ->where('p.enabled = true')
            ->andWhere('np.nameCanonical = :cname')
            ->setParameter('cname', $partnerCName)
            ->getQuery()
            ->getResult();

        if ($withUnifiedReportModuleEnabled === false) {
            return $result;
        }

        return array_filter($result, function(AdNetworkInterface $adNetwork){
            return $adNetwork->getPublisher()->hasUnifiedReportModule();
        });
    }

    public function getAdNetworkByPublisherAndPartnerCName($publisher, $partnerCName)
    {
        return $this->createQueryBuilder('r')
            ->join('r.networkPartner', 'pn')
            ->where('r.publisher = :publisher')
            ->andWhere('pn.nameCanonical = :cname')
            ->setParameter('publisher', $publisher)
            ->setParameter('cname', $partnerCName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function validateEmailToken($publisherId, $partnerCName, $emailToken)
    {
        $adNetwork = $this->createQueryBuilder('r')
            ->join('r.networkPartner', 'pn')
            ->where('r.publisher = :publisherId')
            ->andWhere('r.emailHookToken = :token')
            ->andWhere('pn.nameCanonical = :cname')
            ->setParameter('publisherId', $publisherId)
            ->setParameter('token', $emailToken)
            ->setParameter('cname', $partnerCName)
            ->getQuery()
            ->getOneOrNullResult();

        return $adNetwork instanceof AdNetworkInterface;
    }
}