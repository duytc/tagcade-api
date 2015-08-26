<?php

namespace Tagcade\Form\Type;

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
            ->add('channel', 'entity', ['class' => Channel::class])
            ->add('site', 'entity', ['class' => Site::class]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => ChannelSite::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_channel_site';
    }
}