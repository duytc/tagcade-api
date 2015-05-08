<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Form\Type\AbstractRoleSpecificFormType;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\UserEntityInterface;

class UserFormType extends AbstractRoleSpecificFormType
{
//    private $userRole;

    public function __construct(UserEntityInterface $userRole)
    {
        $this->setUserRole($userRole);
//        $this->userRole = $userRole;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword')
            ->add('firstName')
            ->add('lastName')
            ->add('company')
            ->add('email')
            ->add('phone')
            ->add('city')
            ->add('state')
            ->add('address')
            ->add('postalCode')
            ->add('country')

            // custom fields
            // even though in the system, roles and modules are simply just symfony2 roles
            // we separate the collection of them
//            ->add('userRoles', 'choice', [
//                'mapped' => false,
//                'empty_data' => null,
//                'multiple' => true,
//                'choices' => [
//                    'ROLE_PUBLISHER' => 'Publisher',
//                    'ROLE_ADMIN'     => 'Admin'
//                ],
//            ])
        ;

        if($this->userRole instanceof AdminInterface){
            $builder
                ->add('enabled')
                ->add('enabledModules', 'choice', [
                    'mapped' => false,
                    'empty_data' => null,
                    'multiple' => true,
                    'choices' => [
                        'MODULE_DISPLAY'         => 'Display',
                        'MODULE_VIDEO'           => 'Video',
                        'MODULE_ANALYTICS'       => 'Analytics',
                        'MODULE_FRAUD_DETECTION' => 'Fraud Detection'
                    ],
                ])
                ->add('billingRate')

                ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    /** @var User $user */
                    $user = $event->getData();
                    $form = $event->getForm();

//                $mainUserRole = $form->get('userRoles')->getData();
                    $modules = $form->get('enabledModules')->getData();

//                if (null !== $mainUserRole) {
//                    $user->setUserRoles((array) $mainUserRole);
//                }

                    if (null !== $modules && is_array($modules)) {
                        $user->setEnabledModules($modules);
                    }
                }
            );

        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'validation_groups' => ['Admin', 'Default'],
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_admin_api_user';
    }
}