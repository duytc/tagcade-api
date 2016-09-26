<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class VideoWaterfallTagItemFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videoWaterfallTag', 'entity', array(
                    'class' => VideoWaterfallTag::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('wft')->select('wft');
                    }
                )
            )
            ->add('strategy', ChoiceType::class, array(
                'choices' => array(
                    'linear' => 'linear',
                    'parallel' => 'parallel'
                ),
            ))
            ->add('position');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                /**
                 * @var VideoWaterfallTagItemInterface $waterfallTagItem
                 */
                $waterfallTagItem = $event->getData();
                $waterfallTag = $waterfallTagItem->getVideoWaterfallTag();
                $waterfallTag->addVideoWaterfallTagItem($waterfallTagItem);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => VideoWaterfallTagItem::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_video_waterfall_tag_item';
    }
}