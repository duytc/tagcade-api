<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\WaterfallPlacementRule;
use Tagcade\Service\StringUtilTrait;

class WaterfallPlacementRuleFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profitType')
            ->add('profitValue')
            ->add('publishers')
            ->add('libraryVideoDemandAdTag');
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