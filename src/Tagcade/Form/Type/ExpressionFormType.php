<?php

namespace Tagcade\Form\Type;

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
            ->add('expectAdSlot', 'entity', ['class' => AdSlotAbstract::class])
            ->add('libraryExpression', 'entity', ['class' => LibraryExpression::class])
        ;
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