<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Repository\Core\WaterfallPlacementRuleRepositoryInterface;

class WaterfallPlacementRuleManager implements WaterfallPlacementRuleManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, WaterfallPlacementRuleRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, WaterfallPlacementRuleInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $site)
    {
        if (!$site instanceof WaterfallPlacementRuleInterface) throw new InvalidArgumentException('expect WaterfallPlacementRuleInterface object');

        $this->om->persist($site);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $site)
    {
        if (!$site instanceof WaterfallPlacementRuleInterface) throw new InvalidArgumentException('expect WaterfallPlacementRuleInterface object');

        $this->om->remove($site);
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
}