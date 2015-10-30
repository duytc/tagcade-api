<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\AdminInterface;

class AdNetworkFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')

            ->add('url')
            ->add('defaultCpmRate')
        ;

        if ($this->userRole instanceof AdminInterface) {
            $builder->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                /** @var AdNetworkInterface $adNetwork */
                $adNetwork = $event->getData();
                // initialize adtags count when creating new ad network;
                if ($adNetwork->getId() === null) {
                    $adNetwork->setActiveAdTagsCount(0);
                    $adNetwork->setPausedAdTagsCount(0);
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AdNetwork::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_ad_network';
    }
}