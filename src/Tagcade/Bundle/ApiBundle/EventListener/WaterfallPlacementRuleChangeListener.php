<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Behaviors\ValidateVideoDemandAdTagAgainstPlacementRuleTrait;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Worker\Manager;

class WaterfallPlacementRuleChangeListener
{
    use ValidateVideoDemandAdTagAgainstPlacementRuleTrait;
    /**
     * @var Manager
     */
    private $manager;

    private $changedRules = [];
    /**
     * WaterfallPlacementRuleChangeListener constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }


    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof WaterfallPlacementRuleInterface) {
            return;
        }

        if ($args->hasChangedField('profitType') || $args->hasChangedField('profitValue')) {
            $this->autoPauseVideoDemandAdTags($em, $entity);
            $this->changedRules[] = $entity->getId();
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof WaterfallPlacementRuleInterface) {
            return;
        }

        $this->manager->deployVideoDemandAdTagForNewPlacementRule($entity->getId());
    }

    protected function autoPauseVideoDemandAdTags(EntityManagerInterface $em, WaterfallPlacementRuleInterface $rule)
    {
        $autoPauseTags = [];
        $autoActiveTags = [];
        $videoDemandAdTagRepository = $em->getRepository(VideoDemandAdTag::class);
        $videoDemandAdTags = $videoDemandAdTagRepository->getVideoDemandAdTagsForWaterfallPlacementRule($rule);
        /** @var VideoDemandAdTagInterface $videoDemandAdTag */
        foreach($videoDemandAdTags as $videoDemandAdTag) {
            if ($this->validateDemandAdTagAgainstPlacementRule($videoDemandAdTag) === false) {
                $autoPauseTags[] = $videoDemandAdTag->getId();
            } else {
                $autoActiveTags[] = $videoDemandAdTag->getId();
            }
        }

        $this->manager->autoPauseVideoDemandAdTags($autoPauseTags);
        $this->manager->autoActiveVideoDemandAdTags($autoActiveTags);
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedRules) < 1)
        {
            return;
        }

        foreach($this->changedRules as $rule) {
            $this->manager->deployVideoDemandAdTagForNewPlacementRule($rule);
        }

        $this->changedRules = [];
    }
}