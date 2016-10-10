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
use Tagcade\Service\StringUtilTrait;

class WaterfallPlacementRuleFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profitType', ChoiceType::class, array(
                'choices' => array(
                    WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN,
                    WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN,
                    WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_MANUAL,
                ),
            ))
            ->add('profitValue')
            ->add('publishers')
            ->add('libraryVideoDemandAdTag');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var WaterfallPlacementRuleInterface $waterfallPlacementRule */
                $waterfallPlacementRule = $event->getData();
                switch ($waterfallPlacementRule->getProfitType()) {
                    case WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN:
                        if ($waterfallPlacementRule->getProfitValue() > $waterfallPlacementRule->getLibraryVideoDemandAdTag()->getSellPrice()) {
                            throw new InvalidArgumentException('invalid "Profit value"');
                        }

                        break;
                    case WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN:
                        if ($waterfallPlacementRule->getProfitValue() > 100) {
                            throw new InvalidArgumentException('invalid "Profit value"');
                        }

                        break;
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