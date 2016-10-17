<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Entity\Core\BillingConfiguration;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\UserEntityInterface;

class BillingConfigurationFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('billingFactor', ChoiceType::class, array(
                'choices'=>[
                    BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY  =>'Slot opportunity',
                    BillingConfiguration::BILLING_FACTOR_VIDEO_IMPRESSION  =>'Video impression',
                    BillingConfiguration::BILLING_FACTOR_VIDEO_VISIT       =>'Visit',
                    BillingConfiguration::BILLING_FACTOR_HEADER_BID_REQUEST       => 'Bid Request'
                ]
            ))
            ->add('tiers')
            ->add('defaultConfig')
            ->add('module', ChoiceType::class, array(
                'empty_data' => null,
                'choices' => [
                    User::MODULE_DISPLAY         => 'Display',
                    User::MODULE_VIDEO_ANALYTICS => 'Video',
                    User::MODULE_VIDEO           => 'VideoAds',
                    User::MODULE_ANALYTICS       => 'Analytics',
                    User::MODULE_FRAUD_DETECTION => 'Fraud Detection',
                    User::MODULE_UNIFIED_REPORT  => 'Unified Report',
                    User::MODULE_SUB_PUBLISHER   => 'Sub Publisher',
                    User::MODULE_HEADER_BIDDING  => 'Header Bidding',
                    User::MODULE_RTB             => 'RealTime Bidding'
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

                switch ($billingConfig->getModule()) {
                    case User::MODULE_DISPLAY:
                        if ($billingConfig->getBillingFactor() !== BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY) {
                            $form->get('billingFactor')->addError(new FormError(sprintf('module "%s" only accepts "%s" as billing factor', User::MODULE_DISPLAY, BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY)));
                            return;
                        }

                        break;
                    case User::MODULE_VIDEO:
                        if ($billingConfig->getBillingFactor() !== BillingConfiguration::BILLING_FACTOR_VIDEO_IMPRESSION) {
                            $form->get('billingFactor')->addError(new FormError(sprintf('module "%s" only accepts "%s" as billing factor', User::MODULE_VIDEO, BillingConfiguration::BILLING_FACTOR_VIDEO_IMPRESSION)));
                            return;
                        }

                        break;
                    case User::MODULE_HEADER_BIDDING:
                        if ($billingConfig->getBillingFactor() !== BillingConfiguration::BILLING_HEADER_BID_REQUEST) {
                            $form->get('billingFactor')->addError(new FormError(sprintf('module "%s" only accepts "%s" as billing factor', User::MODULE_HEADER_BIDDING, BillingConfiguration::BILLING_HEADER_BID_REQUEST)));
                            return;
                        }

                        break;
                    case User::MODULE_VIDEO_ANALYTICS:
                        $factors = [BillingConfiguration::BILLING_FACTOR_VIDEO_IMPRESSION, BillingConfiguration::BILLING_FACTOR_VIDEO_VISIT];
                        if (!in_array($billingConfig->getBillingFactor(), $factors)) {
                            $form->get('billingFactor')->addError(new FormError(sprintf('module "%s" only accepts "%s" as billing factor', User::MODULE_VIDEO_ANALYTICS, implode(',', $factors))));
                            return;
                        }

                        break;
                }

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