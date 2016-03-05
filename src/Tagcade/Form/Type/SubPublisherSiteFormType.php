<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Site;
use Tagcade\Entity\Core\SubPublisherSite;

class SubPublisherSiteFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', 'entity', array(
                    'class' => Site::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('site')->select('site');
                    }
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => SubPublisherSite::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_sub_publisher_site';
    }
}