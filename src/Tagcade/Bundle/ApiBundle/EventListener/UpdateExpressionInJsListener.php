<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGeneratorInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Form\Type\ExpressionFormType;
use Tagcade\Form\Type\LibraryExpressionFormType;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Entity\Core\Expression;
use Tagcade\Model\Core\ExpressionJsProducibleInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

class UpdateExpressionInJsListener {

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
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if(!empty($this->updatedExpressions)) {
            $em = $args->getEntityManager();
            foreach ($this->updatedExpressions as $exp) {
                $em->merge($exp);
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
                'vars'=> $convertedExpression['vars'],
                'expression' => $convertedExpression['expression']
            ];

            if ($expression instanceof ExpressionInterface) {
                $expInJs['expectedAdSlot'] = $expression->getExpectAdSlot()->getId();
            }
            else if ($expression instanceof LibraryExpressionInterface) {
                $expInJs['expectedLibraryAdSlot'] = $expression->getExpectLibraryAdSlot()->getId();
            }

            if (is_int($expression->getStartingPosition())) {
                $expInJs['startingPosition'] = $expression->getStartingPosition();
            }

            $expression->setExpressionInJs($expInJs);
        }
    }
}