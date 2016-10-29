<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;


class LibraryExpressionFormType extends AbstractRoleSpecificFormType
{
    /** key expression in expressions */
    const KEY_EXPRESSION_DESCRIPTOR = 'expressionDescriptor';

    /*
     * See the details of "expressionDescriptor" element keys in ExpressionInJsGenerator class, example:
     * {
     *     "groupType":"AND",
     *     "groupVal":[
     *         {
     *             "var":"${USER_AGENT}",
     *             "cmp":"contains",
     *             "val":"blackberry",
     *             "type":"string"
     *         }
     *     ]
     * }
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                // Validate expression format
                try {
                    /**
                     * @var LibraryExpressionInterface $libraryExpression
                     */
                    $libraryExpression = $event->getData();

                    if (!($libraryExpression instanceof LibraryExpressionInterface)) {
                        throw new InvalidFormException('libraryExpression null or not is array');
                    }

                    $expressions = $libraryExpression->getExpressions();
                    /**
                     * @var ExpressionInterface $expression
                     */
                    foreach ($expressions as $expression) {
                        if (!($expression instanceof ExpressionInterface)) {
                            throw new InvalidFormException('Expression null or not is array');
                        }

                        $expression->setLibraryExpression($libraryExpression);
                    }

                    if (null === $libraryExpression->getExpectLibraryAdSlot()) {
                        throw new InvalidFormException('expectedLibraryAdSlot does not exist');
                    }

                    if (null === $libraryExpression->getExpressionDescriptor()
                        || !is_array($libraryExpression->getExpressionDescriptor())
                    ) {
                        throw new InvalidFormException('expressionDescriptor null or not is array');
                    }

                    if (count($libraryExpression->getExpressionDescriptor()) != 2) {
                        throw new InvalidFormException('expressionDescriptor must contain both "groupVal" and "groupType"');
                    }

                    // validate expression descriptor
                    ExpressionInJsGenerator::validateExpressionDescriptor($libraryExpression->getExpressionDescriptor());

                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();
                    $form->get(self::KEY_EXPRESSION_DESCRIPTOR)->addError(new FormError($ex->getMessage()));
                }
            }
        );

        $builder
            ->add('name')
            ->add('expressionDescriptor')
            ->add('expectLibraryAdSlot', 'entity', array(
                'class' => LibraryAdSlotAbstract::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('libSlot')->select('libSlot');
                }
            ))
            ->add('startingPosition')
            ->add('expressions', 'collection', array(
                    'type' => new ExpressionFormType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => LibraryExpression::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_library_expression';
    }
}