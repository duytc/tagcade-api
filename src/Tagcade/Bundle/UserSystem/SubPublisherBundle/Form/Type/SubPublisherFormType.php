<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserSystem\SubPublisherBundle\Entity\User;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Form\Type\AbstractRoleSpecificFormType;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword')
            ->add('email')
            ->add('demandSourceTransparency')
            ->add('enableViewTagcadeReport');

        if ($this->userRole instanceof AdminInterface) {
            $builder->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }

        if (!$this->userRole instanceof SubPublisherInterface) {
            $builder->add('enabled');
            $builder->add('demandSourceTransparency');
            $builder->add('enableViewTagcadeReport');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'validation_groups' => ['Admin', 'Publisher', 'Default'],
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_sub_publisher_api_user';
    }
}