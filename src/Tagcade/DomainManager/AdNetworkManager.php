<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;

class AdNetworkManager implements AdNetworkManagerInterface
{
    protected $om;
    protected $repository;
    protected $unifiedReportMailDomain;

    public function __construct(ObjectManager $om, AdNetworkRepositoryInterface $repository, $unifiedReportMailDomain)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->unifiedReportMailDomain = $unifiedReportMailDomain;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdNetworkInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $adNetwork)
    {
        if(!$adNetwork instanceof AdNetworkInterface) throw new InvalidArgumentException('expect AdNetworkInterface object');

        $this->om->persist($adNetwork);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adNetwork)
    {
        if(!$adNetwork instanceof AdNetworkInterface) throw new InvalidArgumentException('expect AdNetworkInterface object');

        $this->om->remove($adNetwork);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdNetworksForPublisher($publisher, $limit, $offset);
    }

    public function getAdNetworksThatHavePartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdNetworksThatHavePartnerForPublisher($publisher, $limit, $offset);
    }

    public function getAdNetworksThatHavePartnerForSubPublisher(SubPublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdNetworksThatHavePartnerForSubPublisher($publisher, $limit, $offset);
    }

    public function allHasCap($limit = null, $offset = null)
    {
        return $this->repository->allHasCap($limit, $offset);
    }

    public function validateEmailHookToken($publisherId, $partnerCName, $token)
    {
        return $this->repository->validateEmailToken($publisherId, $partnerCName, $token);
    }

    public function getUnifiedReportEmail(AdNetworkInterface $adNetwork, $resetToken = false)
    {
        if ($resetToken === true) {
            $adNetwork->setEmailHookToken(uniqid(''));
            $this->save($adNetwork);
        }

        if (empty($adNetwork->getEmailHookToken())) {
            return '';
        }

        return sprintf('pub%d.%s.%s@%s', $adNetwork->getPublisherId(), $adNetwork->getNetworkPartner()->getNameCanonical(), $adNetwork->getEmailHookToken(), $this->unifiedReportMailDomain);
    }
}