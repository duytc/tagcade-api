<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\SubPublisherPartnerRevenue;
use Tagcade\Model\User\Role\PublisherInterface;

class SubPublisherPartnerRevenueFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adNetworkPartner', 'entity', array(
                    'class' => AdNetworkPartner::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('anp')->select('anp');
                    }
                )
            )
            ->add('revenueOption')
            ->add('revenueValue');

        if ($this->userRole instanceof PublisherInterface) {
            $builder->add(
                $builder->create('subPublisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                /* validate revenueOption is supported or not */
                $revenueOption = $form->get('revenueOption')->getData();

                if (!SubPublisherPartnerRevenue::isSupportedRevenueOption($revenueOption)) {
                    $form->get('revenueOption')->addError(new FormError(sprintf('not supported revenueOption %s', $revenueOption)));
                    return;
                }

                /* validate matching for pair of revenueOption and revenueValue */
                $revenueValue = $form->get('revenueValue')->getData();

                if (!SubPublisherPartnerRevenue::isSupportedRevenueOptionAndValue($revenueOption, $revenueValue)) {
                    $form->get('revenueValue')->addError(new FormError(sprintf('revenueValue %s is invalid for revenueOption %s', $revenueValue, $revenueOption)));
                    return;
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => \Tagcade\Entity\Core\SubPublisherPartnerRevenue::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_sub_publisher_partner_revenue';
    }
}