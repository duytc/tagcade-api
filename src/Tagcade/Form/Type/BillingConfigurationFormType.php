<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\BillingConfiguration;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\User\Role\AdminInterface;

class BillingConfigurationFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('billingFactor', ChoiceType::class, array(
                'choices'=>[
                    'SLOT_OPPORTUNITY'  =>'Slot opportunity',
                    'VIDEO_IMPRESSION'  =>'Video impression',
                    'VISIT'             =>'Visit',
                    'BID_REQUEST'       => 'Bid Request'
                ]
            ))
            ->add('tiers')
            ->add('defaultConfig')
            ->add('module', ChoiceType::class, array(
                'empty_data' => null,
                'choices' => [
                    'MODULE_DISPLAY'         => 'Display',
                    'MODULE_VIDEO_ANALYTICS' => 'Video',
                    'MODULE_VIDEO'         => 'VideoAds',
                    'MODULE_ANALYTICS'       => 'Analytics',
                    'MODULE_FRAUD_DETECTION' => 'Fraud Detection',
                    'MODULE_UNIFIED_REPORT'  => 'Unified Report',
                    'MODULE_SUB_PUBLISHER'  => 'Sub Publisher',
                    'MODULE_HEADER_BIDDING'  => 'Header Bidding',
                    'MODULE_RTB'  => 'RealTime Bidding'
                ],
            ))
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
                /** @var BillingConfigurationInterface $billingConfig */
                $billingConfig = $event->getData();
                $form = $event->getForm();
                $tiers = $billingConfig->getTiers();

                if ((null === $tiers || !is_array($tiers) || empty($tiers)) && ($billingConfig->getDefaultConfig() === false )) {
                    $form->get('tiers')->addError(new FormError('Default config must set true in case tiers is null'));
                    return;
                }

                if (is_array($tiers) && ($billingConfig->getDefaultConfig() === false)){
                    foreach($tiers as $tier) {
                        foreach($tier as $key=>$value) {
                            if (!is_numeric($value) || $value <0){
                                $form->get('tiers')->addError(new FormError('Either threshold or cpmRate is invalid'));
                            }

                            if (!in_array($key, ['cpmRate', 'threshold'])) {
                                $form->get('tiers')->addError(new FormError(sprintf('key "%s" is not supported', $key)));
                            }
                        }
                    }

                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => BillingConfiguration::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_billing_configuration';
    }
}