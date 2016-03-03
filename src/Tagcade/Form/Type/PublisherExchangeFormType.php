<?php

namespace Tagcade\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Exchange;
use Tagcade\Entity\Core\PublisherExchange;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;

class PublisherExchangeFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            )
            ->add('exchange', EntityType::class, array (
                    'class' => Exchange::class,
                )
            )
        ;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => PublisherExchange::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_publisher_exchange';
    }
}