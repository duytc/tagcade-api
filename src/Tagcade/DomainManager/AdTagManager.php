<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Exception\InvalidArgumentException;
use ReflectionClass;

class AdTagManager implements AdTagManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, AdTagRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(AdTagInterface $adTag)
    {
        $this->om->persist($adTag);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(AdTagInterface $adTag)
    {
        $this->om->remove($adTag);
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
    public function getAdTagsForAdSlot(AdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdSlot($adSlot, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function reorderAdTags(array $adTags, array $newAdTagOrderIds)
    {
        if (empty($adTags)) {
            return [];
        }

        $adTagIds = array_map(function(AdTagInterface $adTag) {
            return $adTag->getId();
        }, $adTags);

        $adTags = array_combine($adTagIds, $adTags);

        if (count($newAdTagOrderIds) !== count(array_unique($newAdTagOrderIds))) {
            throw new InvalidArgumentException("Every ad tag id must be unique");
        }

        if (count(array_diff($newAdTagOrderIds, $adTagIds)) !== 0) {
            throw new InvalidArgumentException("There must be a matching new ad tag id order for every ad tag");
        }

        $orderedAdTags = array_map(function($id) use ($adTags) {
            return $adTags[$id];
        }, $newAdTagOrderIds);

        $position = 1;

        foreach($orderedAdTags as $adTag) {
            /** @var AdTagInterface $adTag */
            $adTag->setPosition($position);

            $this->repository->saveAdTagPosition($adTag);

            $position++;
        }

        unset($adTag);

        return array_values($orderedAdTags);
    }
}