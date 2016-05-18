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
use Tagcade\Form\Type\SubPublisherPartnerRevenueFormType;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
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

        $builder->add('subPublisherPartnerRevenue', 'collection', array(
                'mapped' => true,
                'type' => new SubPublisherPartnerRevenueFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

        //$builder->addEventListener(
        //    FormEvents::PRE_SET_DATA,
        //    function (FormEvent $event) {
        //        $form = $event->getForm();
        //
        //         // validate 'demandSourceTransparency' before submitting
        //         if ($this->userRole instanceof PublisherInterface && !$this->userRole->hasUnifiedReportModule()) {
        //             if ($form->has('demandSourceTransparency') && $form->get('demandSourceTransparency')->getData() !== null) {
        //                 $form->get('demandSourceTransparency')->addError(new FormError('this sub publisher belongs to publisher that does not have unified report module enabled'));
        //                 return;
        //             }
        //         }
        //    }
        //);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var SubPublisherInterface $subPublisher */
                $subPublisher = $event->getData();
                $form = $event->getForm();

                /* validate partners and re-mapping subPublisher and subPublisherPartnerRevenue to relationship for cascading */
                /** @var Collection|SubPublisherPartnerRevenueInterface[] $subPublisherPartnerRevenues */
                $subPublisherPartnerRevenues = $event->getForm()->get('subPublisherPartnerRevenue')->getData();

                if ($subPublisherPartnerRevenues === null) {
                    $form->get('subPublisherPartnerRevenue')->addError(new FormError('subPublisherPartnerRevenue must be an array string'));
                    return;
                }

                // validate
                $ownPublisherId = $subPublisher->getPublisher()->getId();
                foreach ($subPublisherPartnerRevenues as $sppr) {
                    if (!in_array($ownPublisherId, $sppr->getAdNetworkPartner()->getPublisherIds())) {
                        $form->get('subPublisherPartnerRevenue')->addError(new FormError('adNetworkPartner is not allowed by own Publisher'));
                        return;
                    }
                }

                // re-mapping
                foreach ($subPublisherPartnerRevenues as $sppr) {
                    if (!$sppr->getSubPublisher() instanceof SubPublisherInterface) {
                        $sppr->setSubPublisher($subPublisher);
                    }
                }

                if ($subPublisherPartnerRevenues instanceof Collection) {
                    $subPublisherPartnerRevenues = $subPublisherPartnerRevenues->toArray();
                }

                $subPublisherPartnerRevenues = array_unique($subPublisherPartnerRevenues);
                $subPublisher->setSubPublisherPartnerRevenue($subPublisherPartnerRevenues);
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