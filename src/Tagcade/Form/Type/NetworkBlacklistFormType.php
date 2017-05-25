<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\DisplayBlacklist;
use Tagcade\Entity\Core\NetworkBlacklist;

class NetworkBlacklistFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adNetwork', 'entity', array(
                    'class' => AdNetwork::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('a')->select('a'); }
                ))
            ->add('displayBlacklist', 'entity', array(
                    'class' => DisplayBlacklist::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('d')->select('d'); }
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => NetworkBlacklist::class
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_network_blacklist';
    }
}