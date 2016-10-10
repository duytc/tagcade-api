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
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Core\WaterfallPlacementRule;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Repository\Core\VideoPublisherRepositoryInterface;

class LibraryVideoDemandAdTagFormType extends AbstractRoleSpecificFormType
{
    use ValidateVideoTargetingTrait;

    /**
     * @var VideoPublisherRepositoryInterface
     */
    private $videoPublisherRepository;

    /**
     * LibraryVideoDemandAdTagFormType constructor.
     * @param VideoPublisherRepositoryInterface $videoPublisherRepository
     */
    public function __construct(VideoPublisherRepositoryInterface $videoPublisherRepository)
    {
        $this->videoPublisherRepository = $videoPublisherRepository;
    }

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

                    if ($libraryVideoDemandAdTag->getSellPrice() === null &&
                        (
                            $waterfallPlacementRule->getProfitType() === WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_FIX_MARGIN ||
                            $waterfallPlacementRule->getProfitType() === WaterfallPlacementRule::PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN
                        )
                    ) {
                        throw new InvalidArgumentException('set the "Sell Price" explicitly to apply placement rules');
                    }

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

                    $publisherIds = $waterfallPlacementRule->getPublishers();
                    foreach($publisherIds as $id) {
                        $publisher = $this->videoPublisherRepository->find($id);
                        if (!$publisher instanceof VideoPublisherInterface) {
                            throw new InvalidArgumentException(sprintf('video publisher %d does not exist', $id));
                        }
                    }
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