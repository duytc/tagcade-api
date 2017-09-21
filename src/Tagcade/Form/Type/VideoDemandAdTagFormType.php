<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryVideoDemandAdTag;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Repository\Core\VideoPublisherRepositoryInterface;

class VideoDemandAdTagFormType extends AbstractRoleSpecificFormType
{
    /**
     * @var VideoPublisherRepositoryInterface
     */
    private $videoPublisherRepository;

    /**
     * VideoDemandAdTagFormType constructor.
     *  @param VideoPublisherRepositoryInterface $videoPublisherRepository
     */
    public function __construct(VideoPublisherRepositoryInterface $videoPublisherRepository)
    {
        $this->videoPublisherRepository = $videoPublisherRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priority')
            ->add('rotationWeight')
            ->add('active')
            ->add(ExpressionInterface::TARGETING)
            ->add('requestCap')
            ->add('targetingOverride')
            /* NOTE: we support submit 'videoWaterfallTagItemForm' in 2 ways:
             * - id: if videoWaterfallTagItemForm existed
             * - json data (id = null): if videoWaterfallTagItemForm does not exist, so need create new videoWaterfallTagItemForm too
             */
            ->add('videoWaterfallTagItem', 'entity', array(
                    'class' => VideoWaterfallTagItem::class,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('wfti')->select('wfti');
                    }
                )
            )
            /*
             * NOTE: we support submit 'libraryVideoDemandAdTag' in 2 ways:
             * - id: if libraryVideoDemandAdTag existed
             * - json data (id = null): if libraryVideoDemandAdTag does not exist, so need create new libraryVideoDemandAdTag too
             */
            ->add('libraryVideoDemandAdTag', 'entity', array(
                    'class' => LibraryVideoDemandAdTag::class,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('lvdt')->select('lvdt');
                    }
                )
            );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $adTag = $event->getData();

                // use VideoWaterfallTagItemFormType instead of Entity if input is array (not one field 'id')
                if (array_key_exists('videoWaterfallTagItem', $adTag) && is_array($adTag['videoWaterfallTagItem'])) {
                    $form->remove('videoWaterfallTagItem');
                    $form->add('videoWaterfallTagItem', new VideoWaterfallTagItemFormType());
                }

                // use LibraryVideoDemandAdTagFormType instead of Entity if input is array (not one field 'id')
                if (array_key_exists('libraryVideoDemandAdTag', $adTag) && is_array($adTag['libraryVideoDemandAdTag'])) {
                    $form->remove('libraryVideoDemandAdTag');
                    $form->add('libraryVideoDemandAdTag', new LibraryVideoDemandAdTagFormType($this->videoPublisherRepository));
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                /**
                 * @var VideoDemandAdTagInterface $demandAdTag
                 */
                $demandAdTag = $event->getData();
                $waterfallTagItem = $demandAdTag->getVideoWaterfallTagItem();
                $waterfallTagItem->addVideoDemandAdTag($demandAdTag);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => VideoDemandAdTag::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_video_demand_ad_tag';
    }
}