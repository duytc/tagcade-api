<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\LibraryExpression;


class ExpressionFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expectAdSlot', 'entity', array(
                'class' => AdSlotAbstract::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('slot')->select('slot');
                }
            ))
            ->add('libraryExpression', 'entity', array(
                'class' => LibraryExpression::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('libEx')->select('libEx');
                }
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Expression::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_expression';
    }
}