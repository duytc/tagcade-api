<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\ApiBundle\Behaviors\ValidateVideoTargetingTrait;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Service\StringUtilTrait;

class VideoWaterfallTagFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;
    use ValidateVideoTargetingTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('platform')
            ->add('name')
            ->add('adDuration')
            ->add('companionAds')
            ->add('targeting')
            ->add('videoPublisher')
            ->add('buyPrice');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var VideoWaterfallTagInterface $videoWaterfallTag */
                $videoWaterfallTag = $event->getData();

                // generate uuid for video waterfall tag
                if ($videoWaterfallTag->getId() === null) {
                    $videoWaterfallTag->setUuid($this->generateUuidV4());
                    if (!is_int($videoWaterfallTag->getAdDuration())) {
                        $videoWaterfallTag->setAdDuration(VideoWaterfallTag::DEFAULT_AD_DURATION);
                    }
                }

                // validate targeting if has
                $videoWaterfallTagTargeting = $videoWaterfallTag->getTargeting();

                if (is_array($videoWaterfallTagTargeting)) {
                    $this->validateTargeting($videoWaterfallTagTargeting);
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
        $this->validateTargetingKeys($targeting, VideoWaterfallTag::getSupportedTargetingKeys());

        // validate targeting player size
        $this->validateTargetingPlayerSize($targeting);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => VideoWaterfallTag::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_video_waterfall_tag';
    }
}