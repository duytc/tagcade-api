<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Site;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class SiteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('domain')
        ;

        if ($options['current_user_role'] instanceof AdminInterface) {
            $builder->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Site::class,
            ])
            ->setRequired([
                'current_user_role',
            ])
            ->setAllowedTypes([
                'current_user_role' => UserRoleInterface::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_site';
    }
}