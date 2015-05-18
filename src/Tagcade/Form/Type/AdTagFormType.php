<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdSlot;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class AdTagFormType extends AbstractRoleSpecificFormType
{
    const AD_TYPE_HTML = 0;
    const AD_TYPE_IMAGE = 1;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // admins can add adSlots for any publisher
            $builder->add('adSlot');
            $builder->add('adNetwork');

        } else if ($this->userRole instanceof PublisherInterface) {

            /** @var PublisherInterface $publisher */
            $publisher = $this->userRole;

            $builder
                ->add('adSlot', 'entity', [
                    'class' => AdSlot::class,
                    'query_builder' => function(AdSlotRepositoryInterface $repository) use($publisher) {
                        return $repository->getAdSlotsForPublisherQuery($publisher);
                    }
                ])
                ->add('adNetwork', 'entity', [
                    'class' => AdNetwork::class,
                    'query_builder' => function(AdNetworkRepositoryInterface $repository) use ($publisher) {
                        return $repository->getAdNetworksForPublisherQuery($publisher);
                    }
                ])
            ;

            unset($publisher);

        } else {
            throw new LogicException('A valid user role is required by AdTagFormType');
        }

        $builder
            ->add('name')
            ->add('html')
            ->add('position')
            ->add('frequencyCap')
            ->add('active')
            ->add('rotation')
            ->add('adType')
            ->add('descriptor')
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var AdTagInterface $data */
                $data = $event->getData();

                try {
                    $frequencyCap = $data->getFrequencyCap();
                    if (null !== $frequencyCap && (!is_integer($frequencyCap) || $frequencyCap < 1)) {
                        throw new InvalidArgumentException('Frequency cap must be an positive integer');
                    }
                }
                catch (InvalidArgumentException $e) {
                    $form = $event->getForm();

                    $form->get('frequencyCap')->addError(new FormError($e->getMessage()));
                }
                try {
                    switch ($data->getAdType()) {
                        case self::AD_TYPE_IMAGE:
                            $this->validateImageAd($data);
                            break;
                        default:
                            $this->validateCustomAd($data);
                    }
                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();

                    $form->get('descriptor')->addError(new FormError($ex->getMessage()));
                }
            }
        );
    }

    protected function validateImageAd(AdTagInterface $adTag)
    {
        $descriptor = $adTag->getDescriptor();

        if (null === $descriptor || !is_array($descriptor) || !isset($descriptor['imageUrl']) || !isset($descriptor['targetUrl']))
        {
            throw new InvalidFormException('The descriptor "%descriptor%" for AD_TYPE_IMAGE invalid: must contain keys \'imageUrl\' and \'targetUrl\'.', $this);
        }

        $this->validateImageUrl($descriptor['imageUrl']);

        $this->validateTargetUrl($descriptor['targetUrl']);
    }

    protected function validateCustomAd(AdTagInterface $adTag)
    {
         if (null === $adTag->getHtml()) {
             throw new InvalidFormException('expect html of ad tag');
         }
    }

    /**
     * validate ImageUrl.
     * @param $imageUrl
     */
    protected function validateImageUrl($imageUrl)
    {
        if (null === $imageUrl || sizeof($imageUrl) < 0
            || (
                !$this->endsWith($imageUrl, '.jpg')
                && !$this->endsWith($imageUrl, '.jpeg')
                && !$this->endsWith($imageUrl, '.png')
                && !$this->endsWith($imageUrl, '.gif')
                && !$this->endsWith($imageUrl, '.bmp')
            )
        ) {
            throw new InvalidFormException('The descriptor for AD_TYPE_IMAGE invalid: \'imageUrl\' must not null and not empty and end with ".jpg, .jpeg, .png, .gif, .bmp"', $this);
        }

        $this->validateUrl($imageUrl);
    }

    /**
     * validate TargetUrl
     * @param $targetUrl
     */
    protected function validateTargetUrl($targetUrl)
    {
        $this->validateUrl($targetUrl);
    }

    /**
     * validate Url format
     * @param $url
     */
    protected function validateUrl($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)){
            throw new InvalidFormException('The format of url "%url%" is invalid.', $this);
        }
    }

    /**
     * check if string $haystack start with $needle
     * @param $haystack
     * @param $needle
     * @return boolean
     */
    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * check if string $haystack end with $needle
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AdTag::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_ad_tag';
    }
}