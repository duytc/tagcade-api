<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle\Form\Type;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserSystem\SubPublisherBundle\Entity\User;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Form\Type\AbstractRoleSpecificFormType;
use Tagcade\Form\Type\SubPublisherSiteFormType;
use Tagcade\Model\Core\SubPublisherSiteInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword')
            ->add('email');

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
        }

        $builder->add('subPublisherSites', 'collection', array(
                'mapped' => true,
                'type' => new SubPublisherSiteFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var SubPublisherInterface $subPublisher */
                $subPublisher = $event->getData();
                $form = $event->getForm();

                /** @var SubPublisherSiteInterface[] $subPublisherSites */
                $subPublisherSites = $event->getForm()->get('subPublisherSites')->getData();

                if($subPublisherSites === null) {
                    $form->get('subPublisherSites')->addError(new FormError('subPublisherSites must be an array string'));
                    return;
                }

                foreach ($subPublisherSites as $sps) {
                    if (!$sps->getSubPublisher() instanceof SubPublisherInterface) {
                        $sps->setSubPublisher($subPublisher);
                    }
                }

                if ($subPublisherSites instanceof Collection) {
                    $subPublisherSites = $subPublisherSites->toArray();
                }

                $subPublisherSites = array_unique($subPublisherSites);
                $subPublisher->setSubPublisherSites($subPublisherSites);
            }
        );
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