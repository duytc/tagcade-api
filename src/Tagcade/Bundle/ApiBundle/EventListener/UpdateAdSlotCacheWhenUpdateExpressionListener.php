<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\ExpressionJsProducibleInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class UpdateAdSlotCacheWhenUpdateExpressionListener
{
    protected $updatedExpressions = [];

    /** @var  AdSlotRepositoryInterface */
    protected $adSlotRepository;

    /** @var  EventDispatcher */
    protected $dispatcher;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * handle event prePersist one expression, this auto update expressionInJS field.
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setRepository($args->getEntityManager());

        $entity = $args->getObject();
        if (!$entity instanceof ExpressionJsProducibleInterface && !$entity instanceof LibraryAdTagInterface) {
            return;
        }

        $this->updatedExpressions[] = $entity;
    }

    /**
     * handle event preUpdate one expression, this auto update expressionInJS field.
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->setRepository($args->getEntityManager());

        $entity = $args->getObject();
        if (!$entity instanceof ExpressionJsProducibleInterface && !$entity instanceof LibraryAdTagInterface) {
            return;
        }
        $this->updatedExpressions[] = $entity;
    }

    /**
     * handle event preRemove one
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->setRepository($args->getEntityManager());

        $entity = $args->getEntity();

        if (!$entity instanceof ExpressionJsProducibleInterface && !$entity instanceof LibraryAdTagInterface) {
            return;
        }
        $this->updatedExpressions[] = $entity;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $this->setRepository($args->getEntityManager());

        if (!empty($this->updatedExpressions)) {
            $adSlots = $this->filterAdSlotBlackListWhiteListExpression($this->updatedExpressions);

            if (count($adSlots) > 0) {
                $this->dispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));
            }

            $this->updatedExpressions = []; // reset updated expressions
        }
    }

    /**
     * @param array $updateExpressions
     * @return mixed
     */
    private function filterAdSlotBlackListWhiteListExpression($updateExpressions)
    {
        $adSlots = [];
        $adSlotIds = [];

        if (!is_array($updateExpressions)) {
            return $adSlots;
        }

        foreach ($updateExpressions as $updateExpression) {
            if ($updateExpression instanceof LibraryExpressionInterface) {
                $libraryDynamicAdSlot = $updateExpression->getLibraryDynamicAdSlot();
                $dynamicAdSlots = $this->adSlotRepository->getDisplayAdSlostByLibrary($libraryDynamicAdSlot);

                foreach ($dynamicAdSlots as $dynamicAdSlot) {
                    if (!$dynamicAdSlot instanceof DynamicAdSlotInterface || in_array($dynamicAdSlot->getId(), $adSlotIds) || $dynamicAdSlot->getDeletedAt() != null) {
                        continue;
                    }

                    $adSlots[] = $dynamicAdSlot;
                }

                continue;
            }

            if ($updateExpression instanceof LibraryAdTagInterface) {
                $reportableAdSlots = $this->adSlotRepository->getAdSlotByLibraryAdTag($updateExpression);

                foreach ($reportableAdSlots as $reportableAdSlot) {
                    if (in_array($reportableAdSlot->getId(), $adSlotIds) || $reportableAdSlot->getDeletedAt() != null) {
                        continue;
                    }

                    $adSlots[] = $reportableAdSlot;
                }
                
                continue;
            }
        }

        return array_values(array_unique($adSlots, SORT_REGULAR));
    }

    /**
     * @param EntityManagerInterface $em
     */
    private function setRepository($em)
    {
        if (!$this->adSlotRepository instanceof AdSlotRepositoryInterface) {
            $this->adSlotRepository = $em->getRepository(AdSlotAbstract::class);
        }
    }
}