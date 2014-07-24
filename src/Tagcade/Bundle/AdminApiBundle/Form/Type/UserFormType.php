<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainPassword')
            ->add('enabled')

            ->add('role', 'choice', [
                'mapped' => false,
                'empty_data' => null,
                'choices' => [
                    'publisher' => 'Publisher',
                    'admin' => 'Admin'
                ],
            ])

            ->add('features', 'choice', [
                'mapped' => false,
                'empty_data' => null,
                'multiple' => true,
                'choices' => [
                    'display' => 'Display',
                    'video' => 'Video',
                    'analytics' => 'Analytics',
                    'fraud_detection' => 'Fraud Detection'
                ],
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var User $user */
                $user = $event->getData();
                $form = $event->getForm();

                $mainUserRole = $form->get('role')->getData();
                $features = $form->get('features')->getData();

                if (null !== $mainUserRole) {
                    $user->setUserRoles((array) $mainUserRole);
                }

                if (null !== $features && is_array($features)) {
                    $user->setEnabledFeatures($features);
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'validation_groups' => ['Manage', 'Default'],
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_admin_api__user';
    }
}