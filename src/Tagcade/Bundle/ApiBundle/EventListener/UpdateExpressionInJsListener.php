<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGeneratorInterface;
use Tagcade\Entity\Core\BlacklistExpression;
use Tagcade\Entity\Core\DisplayBlacklist;
use Tagcade\Entity\Core\DisplayWhiteList;
use Tagcade\Entity\Core\WhiteListExpression;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\ExpressionJsProducibleInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

class UpdateExpressionInJsListener
{
    protected $updatedExpressions = [];
    /**
     * @var ExpressionInJsGeneratorInterface
     */
    private $expressionInJsGenerator;

    function __construct(ExpressionInJsGeneratorInterface $expressionInJsGenerator)
    {
        $this->expressionInJsGenerator = $expressionInJsGenerator;
    }

    /**
     * handle event prePersist one expression, this auto update expressionInJS field.
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof ExpressionJsProducibleInterface) {
            return;
        }

        $this->createExpressionInJs($entity);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof LibraryExpressionInterface) {
            $this->createDomainMappingForLibraryExpression($entity, $args->getEntityManager());
        }
    }

    /**
     * handle event preUpdate one expression, this auto update expressionInJS field.
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ExpressionJsProducibleInterface) {
            $this->createExpressionInJs($entity);
        }

        if ($entity instanceof LibraryExpressionInterface && ($args->hasChangedField('expressionDescriptor') || $args->hasChangedField('startingPosition'))) {
            $expressions = $entity->getExpressions();
            foreach ($expressions as $exp) {
                $this->createExpressionInJs($exp);
                $this->updatedExpressions[] = $exp;
            }

            $this->createDomainMappingForLibraryExpression($entity, $args->getEntityManager());
        }
    }

    protected function createDomainMappingForLibraryExpression(LibraryExpressionInterface $libraryExpression, EntityManagerInterface $em)
    {
        $descriptor = $libraryExpression->getExpressionDescriptor();
        if (is_array($descriptor) && count($descriptor) >= 2) {
            $this->createDomainMappingForDescriptor($descriptor, $libraryExpression, $em);
        }
    }

    protected function createDomainMappingForDescriptor($descriptor, LibraryExpressionInterface $libraryExpression, EntityManagerInterface $em)
    {
        $groupType = (isset($descriptor[ExpressionInJsGenerator::KEY_GROUP_TYPE])) ? ExpressionInJsGenerator::$VAL_GROUPS[$descriptor[ExpressionInJsGenerator::KEY_GROUP_TYPE]] : null;
        if ($groupType != null) {
            $this->createDomainMappingForGroupObject($descriptor, $libraryExpression, $em);
        } else {
            $this->createDomainMappingForConditionObject($descriptor, $libraryExpression, $em);
        }
    }

    protected function createDomainMappingForGroupObject($descriptor, LibraryExpressionInterface $libraryExpression, EntityManagerInterface $em)
    {
        foreach ($descriptor[ExpressionInJsGenerator::KEY_GROUP_VAL] as $expression) {
            $this->createDomainMappingForDescriptor($expression, $libraryExpression, $em);
        }
    }

    protected function createDomainMappingForConditionObject($expression, LibraryExpressionInterface $libraryExpression, EntityManagerInterface $em)
    {
        $blacklistExpressionRepository = $em->getRepository(BlacklistExpression::class);
        $whiteListExpressionRepository= $em->getRepository(WhiteListExpression::class);
        $displayBlacklistRepository = $em->getRepository(DisplayBlacklist::class);
        $displayWhiteListRepository = $em->getRepository(DisplayWhiteList::class);

        if (
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAR, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_CMP, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAL, $expression) &&
            $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAR] == '${DOMAIN}'
        ) {
            if (in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inBlacklist', 'notInBlacklist'])) {
                $id = intval($expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                if (!$blacklistExpressionRepository->checkLibraryExpressionExist($libraryExpression, $id)) {
                    $blacklist = $displayBlacklistRepository->find($id);
                    if ($blacklist instanceof DisplayBlacklistInterface) {
                        $blacklistExpression = (new BlacklistExpression())->setBlacklist($blacklist)->setLibraryExpression($libraryExpression);
                        $this->updatedExpressions[] = $blacklistExpression;
                    }
                }
            }

            if (in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inWhitelist', 'notInWhitelist'])) {
                $id = intval($expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                if (!$whiteListExpressionRepository->checkLibraryExpressionExist($libraryExpression, $id)) {
                    $whiteList = $displayWhiteListRepository->find($id);
                    if ($whiteList instanceof DisplayWhiteListInterface) {
                        $whiteListExpression = (new WhiteListExpression())->setWhiteList($whiteList)->setLibraryExpression($libraryExpression);
                        $this->updatedExpressions[] = $whiteListExpression;
                    }
                }
            }
        }
    }


    public function postFlush(PostFlushEventArgs $args)
    {
        if (!empty($this->updatedExpressions)) {
            $em = $args->getEntityManager();
            foreach ($this->updatedExpressions as $exp) {
                $em->persist($exp);
            }

            $this->updatedExpressions = []; // reset updated expressions

            $em->flush();
        }
    }

    protected function createExpressionInJs(ExpressionJsProducibleInterface $expression)
    {
        $convertedExpression = $this->expressionInJsGenerator->generateExpressionInJs($expression);

        if (null !== $convertedExpression) {
            $expInJs = [
                'vars' => $convertedExpression['vars'],
                'expression' => $convertedExpression['expression'],
                'domainChecks' => $convertedExpression['domainChecks']
            ];

            if ($expression instanceof ExpressionInterface) {
                $expInJs['expectedAdSlot'] = $expression->getExpectAdSlot()->getId();

                if ($expression->getExpectAdSlot() instanceof DisplayAdSlotInterface) {
                    $expInJs['cpm'] = $expression->getHbBidPrice();
                }
            } else if ($expression instanceof LibraryExpressionInterface) {
                $expInJs['expectedLibraryAdSlot'] = $expression->getExpectLibraryAdSlot()->getId();
            }

            if (is_int($expression->getStartingPosition())) {
                $expInJs['startingPosition'] = $expression->getStartingPosition();
            }

            $expression->setExpressionInJs($expInJs);
        }
    }
}