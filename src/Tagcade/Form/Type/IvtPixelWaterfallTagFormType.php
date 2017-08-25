<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\IvtPixel;
use Tagcade\Entity\Core\IvtPixelWaterfallTag;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;

class IvtPixelWaterfallTagFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('waterfallTag', 'entity', array(
                    'class' => VideoWaterfallTag::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('wft')->select('wft');
                    }
                )
            )
            ->add('ivtPixel', 'entity', array(
                    'class' => IvtPixel::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('ip')->select('ip');
                    }
                )
            );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                /**
                 * @var IvtPixelWaterfallTagInterface $ivtPixelWaterfallTag
                 */
                $ivtPixelWaterfallTag = $event->getData();
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => IvtPixelWaterfallTag::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ivt_pixel_waterfall_tag';
    }
}