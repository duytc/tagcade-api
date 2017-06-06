<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\BlacklistExpression;
use Tagcade\Entity\Core\WhiteListExpression;
use Tagcade\Model\Core\BlacklistExpressionInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\WhiteListExpressionInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\BlacklistExpressionRepositoryInterface;
use Tagcade\Repository\Core\WhiteListExpressionRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork\AdNetworkInterface;

class UpdateAdSlotCacheWhenDeleteBlacklistWhiteListListener
{
    /** @var  EventDispatcher */
    protected $dispatcher;

    protected $changedEntities = [];

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * handle event preRemove one
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof DisplayBlacklistInterface) {
            $blacklistExpressionRepository = $em->getRepository(BlacklistExpression::class);
            $blacklistExpressions = $blacklistExpressionRepository->getByBlackList($entity);

            /** @var BlacklistExpressionInterface $blacklistExpression */
            foreach ($blacklistExpressions as $blacklistExpression) {
                if ($blacklistExpression->getLibraryAdTag() instanceof LibraryAdTagInterface) {
                    $this->updateBlacklistExpressionForLibraryAdTag($blacklistExpression->getLibraryAdTag(), $entity->getId());
                }

                if ($blacklistExpression->getLibraryExpression() instanceof LibraryExpressionInterface) {
                    $this->updateBlacklistExpressionForLibraryExpression($blacklistExpression->getLibraryExpression(), $entity->getId());
                }

                $em->remove($blacklistExpression);
            }
        }

        if ($entity instanceof DisplayWhiteListInterface) {
            $whiteListExpressionRepository = $em->getRepository(WhiteListExpression::class);
            $whiteListExpressions = $whiteListExpressionRepository->getByWhiteList($entity);

            /** @var WhiteListExpressionInterface $whiteListExpression */
            foreach ($whiteListExpressions as $whiteListExpression) {
                if ($whiteListExpression->getLibraryAdTag() instanceof LibraryAdTagInterface) {
                    $this->updateWhiteListExpressionForLibraryAdTag($whiteListExpression->getLibraryAdTag(), $entity->getId());
                }

                if ($whiteListExpression->getLibraryExpression() instanceof LibraryExpressionInterface) {
                    $this->updateWhiteListExpressionForLibraryExpression($whiteListExpression->getLibraryExpression(), $entity->getId());
                }

                $em->remove($whiteListExpression);
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedEntities) < 1) {
            return;
        }

        $em = $args->getEntityManager();
        foreach ($this->changedEntities as $changedEntity) {
            $em->merge($changedEntity);
        }

        $this->changedEntities = [];
        $em->flush();
    }

    protected function updateBlacklistExpressionForLibraryAdTag(LibraryAdTagInterface $libraryAdTag, $blacklistId)
    {
        $descriptor = $libraryAdTag->getExpressionDescriptor();
        $descriptor = $this->updateDomainExpression($descriptor, $blacklistId);
        $libraryAdTag->setExpressionDescriptor($descriptor);
        $this->changedEntities[] = $libraryAdTag;
    }

    protected function updateBlacklistExpressionForLibraryExpression(LibraryExpressionInterface $libraryExpression, $blacklistId)
    {
        $descriptor = $libraryExpression->getExpressionDescriptor();
        $descriptor = $this->updateDomainExpression($descriptor, $blacklistId);
        $libraryExpression->setExpressionDescriptor($descriptor);
        $this->changedEntities[] = $libraryExpression;
    }

    protected function updateWhiteListExpressionForLibraryAdTag(LibraryAdTagInterface $libraryAdTag, $whiteListId)
    {
        $descriptor = $libraryAdTag->getExpressionDescriptor();
        $descriptor = $this->updateDomainExpression($descriptor, $whiteListId, $isBlacklist = false);
        $libraryAdTag->setExpressionDescriptor($descriptor);
        $this->changedEntities[] = $libraryAdTag;
    }

    protected function updateWhiteListExpressionForLibraryExpression(LibraryExpressionInterface $libraryExpression, $whiteListId)
    {
        $descriptor = $libraryExpression->getExpressionDescriptor();
        $descriptor = $this->updateDomainExpression($descriptor, $whiteListId, $isBlacklist = false);
        $libraryExpression->setExpressionDescriptor($descriptor);
        $this->changedEntities[] = $libraryExpression;
    }

    protected function updateDomainExpression($expression, $id, $isBlacklist = true)
    {
        if (array_key_exists(ExpressionInJsGenerator::KEY_GROUP_VAL, $expression)) {
            return $this->updateDomainExpressionForGroupObject($expression, $id, $isBlacklist);
        }

        return $this->updateDomainExpressionForConditionObject($expression, $id, $isBlacklist);
    }

    protected function updateDomainExpressionForConditionObject($expression, $id, $isBlacklist = true)
    {
        if (
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAR, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_CMP, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAL, $expression) &&
            $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAR] == '${DOMAIN}'
        ) {
            if ($isBlacklist && in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inBlacklist', 'notInBlacklist'])) {
                $blacklists = explode(',', $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                foreach ($blacklists as $i=>$blacklist) {
                    if ($blacklist == $id) {
                        unset($blacklists[$i]);
                    }
                }

                $blacklists = array_values($blacklists);

                if (empty($blacklists)) return null;
                $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL] = implode(',', $blacklists);
                return $expression;
            }

            if (!$isBlacklist && in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inWhitelist', 'notInWhitelist'])) {
                $whiteLists = explode(',', $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                foreach ($whiteLists as $i=>$whiteList) {
                    if ($whiteList == $id) {
                        unset($whiteLists[$i]);
                    }
                }

                $whiteLists = array_values($whiteLists);

                if (empty($whiteLists)) return null;
                $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL] = implode(',', $whiteLists);
                return $expression;
            }
        }

        return $expression;
    }

    protected function updateDomainExpressionForGroupObject($expression, $id, $isBlacklist = true)
    {
        $groupVal = $expression[ExpressionInJsGenerator::KEY_GROUP_VAL];
        foreach ($groupVal as $i=>&$descriptor) {
            $descriptor = $this->updateDomainExpression($descriptor, $id, $isBlacklist);
            if ($descriptor == null) {
                unset($groupVal[$i]);
            }
        }

        $expression[ExpressionInJsGenerator::KEY_GROUP_VAL] = array_values($groupVal);
        return $expression;
    }
}