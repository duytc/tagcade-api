<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\WaterfallPlacementRule;

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
            ;
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