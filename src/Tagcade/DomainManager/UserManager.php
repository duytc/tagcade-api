<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\UserEntityInterface;
use ReflectionClass;

class UserManager implements UserManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, ObjectRepository $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function save(UserEntityInterface $user)
    {
        $this->om->persist($user);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(UserEntityInterface $user)
    {
        $this->om->remove($user);
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
        return $this->repository->findAll();
    }
}