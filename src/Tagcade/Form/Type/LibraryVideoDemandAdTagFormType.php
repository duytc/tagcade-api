<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\ApiBundle\Behaviors\ValidateVideoTargetingTrait;
use Tagcade\Entity\Core\LibraryVideoDemandAdTag;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;

class LibraryVideoDemandAdTagFormType extends AbstractRoleSpecificFormType
{
    use ValidateVideoTargetingTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tagURL')
            ->add('name')
            ->add('timeout')
            ->add('targeting')
            ->add('sellPrice')
            ->add('videoDemandPartner', 'entity', array(
                'class' => VideoDemandPartner::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('vdp')->select('vdp');
                }
            ))
            ->add('waterfallPlacementRules', 'collection', array(
                'mapped' => true,
                'type' => new WaterfallPlacementRuleFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            ));

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag */
                $libraryVideoDemandAdTag = $event->getData();
                $waterfallPlacementRules = $libraryVideoDemandAdTag->getWaterfallPlacementRules();

                /** @var WaterfallPlacementRuleInterface $waterfallPlacementRule */
                foreach ($waterfallPlacementRules as $waterfallPlacementRule) {
                    $waterfallPlacementRule->setLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
                }

                // validate targeting if has
                $targeting = $libraryVideoDemandAdTag->getTargeting();

                if (is_array($targeting)) {
                    $this->validateTargeting($targeting);
                }
            }
        );
    }

    /**
     * validateTargeting
     *
     * @param array $targeting
     * @return bool true if passed
     * @throws InvalidArgumentException if not passed
     */
    private function validateTargeting(array $targeting)
    {
        // check if supported targeting keys
        $this->validateTargetingKeys($targeting, LibraryVideoDemandAdTag::getSupportedTargetingKeys());

        // validate targeting player size
        $this->validateTargetingPlayerSize($targeting);

        // validate targeting required macros
        $this->validateTargetingRequiredMacros($targeting);

        // validate targeting platform
        $this->validateTargetingPlatform($targeting);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => LibraryVideoDemandAdTag::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_library_video_demand_ad_tag';
    }
}