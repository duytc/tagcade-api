<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Domain\DTO\Core\WaterfallTagsPlacementRule;
use Tagcade\Entity\Core\WaterfallPlacementRule;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryVideoDemandAdTagRepositoryInterface;
use Tagcade\Service\Core\VideoDemandAdTag\DeployLibraryVideoDemandAdTagServiceInterface;

class LibraryVideoDemandAdTagManager implements LibraryVideoDemandAdTagManagerInterface
{
    /** @var ObjectManager */
    protected $om;
    /** @var LibraryVideoDemandAdTagRepositoryInterface */
    protected $repository;
    /** @var DeployLibraryVideoDemandAdTagServiceInterface */
    protected $deployLibraryVideoDemandAdTagService;

    public function __construct(ObjectManager $om, LibraryVideoDemandAdTagRepositoryInterface $repository, DeployLibraryVideoDemandAdTagServiceInterface $deployLibraryVideoDemandAdTagService)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->deployLibraryVideoDemandAdTagService = $deployLibraryVideoDemandAdTagService;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryVideoDemandAdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $videoDemandAdTag)
    {
        if (!$videoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) throw new InvalidArgumentException('expect LibraryVideoDemandAdTagInterface object');

        $this->om->persist($videoDemandAdTag);
        $this->om->flush();
    }

    public function deployLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $videoDemandAdTag)
    {
        $validWaterfallTags = $this->deployLibraryVideoDemandAdTagService->getValidVideoWaterfallTagsForLibraryVideoDemandAdTag($videoDemandAdTag);

        /** @var WaterfallTagsPlacementRule $validWaterfallTag */
        foreach($validWaterfallTags as $validWaterfallTag) {
            $placementRule = $validWaterfallTag->getPlacementRule();
            $this->deployLibraryVideoDemandAdTagService->deployLibraryVideoDemandAdTagToWaterfalls(
                $videoDemandAdTag,
                $placementRule,
                $validWaterfallTag->getWaterfallTags(),
                null,
                false,
                $placementRule->getPriority(),
                $placementRule->getRotationWeight(),
                $placementRule->isActive(),
                $placementRule->getPosition(),
                $placementRule->isShiftDown()
            );
        }
    }

    public function deployLibraryVideoDemandAdTagBasedOnManualPlacementRule(LibraryVideoDemandAdTagInterface $videoDemandAdTag)
    {
        $placementRules = $videoDemandAdTag->getWaterfallPlacementRules()->toArray();
        $placementRules = array_filter($placementRules, function(WaterfallPlacementRuleInterface $rule) {
            return $rule->getProfitType() === WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_MANUAL;
        });

        /**
         * @var WaterfallPlacementRuleInterface $placementRule
         */
        foreach($placementRules as $placementRule) {
            if (empty($placementRule->getWaterfalls())) {
                continue;
            }

            $this->deployLibraryVideoDemandAdTagService->deployLibraryVideoDemandAdTagToWaterfalls(
                $videoDemandAdTag,
                $placementRule,
                $placementRule->getWaterfalls(),
                null,
                false,
                $placementRule->getPriority(),
                $placementRule->getRotationWeight(),
                $placementRule->isActive(),
                $placementRule->getPosition(),
                $placementRule->isShiftDown()
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $videoDemandAdTag)
    {
        if (!$videoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) throw new InvalidArgumentException('expect LibraryVideoDemandAdTagInterface object');

        if (count($videoDemandAdTag->getVideoDemandAdTags()) > 0) {
            throw new RuntimeException('There are some Video Demand Ad Tag still referencing to this entity');
        }

        $this->om->remove($videoDemandAdTag);
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
     * @param ModelInterface $site
     * @return mixed|void
     */
    public function persists(ModelInterface $site)
    {
        $this->om->persist($site);
    }

    /**
     * Flush message to data base
     */
    public function flush()
    {
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function getLibraryVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryVideoDemandAdTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getLibraryVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryVideoDemandAdTagsForDemandPartner($videoDemandPartner, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function generateVideoDemandAdTagsFromLibraryForVideoWaterfallTags(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, array $videoWaterfallTags)
    {
        $this->deployLibraryVideoDemandAdTagService->deployLibraryVideoDemandAdTagToWaterfalls($libraryVideoDemandAdTag, $videoWaterfallTags);
    }

    /**
     * @inheritdoc
     */
    public function findByNameAndVideoDemandPartner($name, VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        return $this->repository->findByNameAndVideoDemandPartner($name, $videoDemandPartner, $limit, $offset);
    }
}