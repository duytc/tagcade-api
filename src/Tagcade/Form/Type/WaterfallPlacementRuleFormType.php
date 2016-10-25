<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\WaterfallPlacementRule;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;

class WaterfallPlacementRuleFormType extends AbstractRoleSpecificFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profitType', ChoiceType::class, array(
                'choices' => array(
                    WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN => WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN,
                    WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN => WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN,
                    WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_MANUAL => WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_MANUAL,
                ),
            ))
            ->add('profitValue')
            ->add('publishers')
            ->add('waterfalls')
            ->add('position')
            ->add('shiftDown')
            ->add('active')
            ->add('priority')
            ->add('rotationWeight')
            ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var WaterfallPlacementRuleInterface $placementRule */
                $placementRule = $event->getData();

                if (!is_array($placementRule->getWaterfalls())) {
                    throw new InvalidArgumentException('expect "waterfalls" to be an array');
                }

                if (!is_array($placementRule->getPublishers())) {
                    throw new InvalidArgumentException('expect "publishers" to be an array');
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => WaterfallPlacementRule::class
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_waterfall_placement_rule';
    }
}