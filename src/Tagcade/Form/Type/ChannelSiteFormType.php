<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Channel;
use Tagcade\Entity\Core\ChannelSite;
use Tagcade\Entity\Core\Site;

class ChannelSiteFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('channel', 'entity', array(
                    'class' => Channel::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('cn')->select('cn'); }
                ))
            ->add('site', 'entity', array(
                    'class' => Site::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('site')->select('site'); }
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => ChannelSite::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_channel_site';
    }
}