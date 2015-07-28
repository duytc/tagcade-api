<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class AdTagFormType extends AbstractRoleSpecificFormType
{
    protected $adSlotRepository;

    const AD_TYPE_HTML = 0;
    const AD_TYPE_IMAGE = 1;

    public function __construct(AdSlotRepositoryInterface $adSlotRepository){
        $this->adSlotRepository = $adSlotRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // admins can add adSlots for any publisher
            $builder->add('adSlot');

        }
        else if ($this->userRole instanceof PublisherInterface) {

            $builder
                ->add('adSlot', 'entity', array(
                    'class' => AdSlotAbstract::class,
                    'choices' => $this->getReportableAdSlotsForPublisher($this->userRole)
                ));

        } else {
            throw new LogicException('A valid user role is required by AdTagFormType');
        }

        $builder
            ->add('name')
            ->add('position')
            ->add('frequencyCap')
            ->add('active')
            ->add('rotation')
            ->add('libraryAdTag', 'entity', array('class' => LibraryAdTag::class))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $adTag = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdTag', $adTag) && is_array($adTag['libraryAdTag'])){
                    $form->remove('libraryAdTag');
                    $form->add('libraryAdTag', new LibraryAdTagFormType($this->userRole));
                }
            }
        );
    }

    /**
     * get all adSlots (include display-adSlots and native-adSlots) for Publisher
     * @param PublisherInterface $publisher
     * @return array
     */
    protected function getReportableAdSlotsForPublisher(PublisherInterface $publisher) {

        return $this->adSlotRepository->getReportableAdSlotsForPublisher($publisher);
    }

//    protected function validateImageAd(AdTagInterface $adTag)
//    {
//        $descriptor = $adTag->getDescriptor();
//
//        if (null === $descriptor || !is_array($descriptor) || !isset($descriptor['imageUrl']) || !isset($descriptor['targetUrl']))
//        {
//            throw new InvalidFormException('The descriptor "%descriptor%" for AD_TYPE_IMAGE invalid: must contain keys \'imageUrl\' and \'targetUrl\'.', $this);
//        }
//
//        $this->validateImageUrl($descriptor['imageUrl']);
//
//        $this->validateTargetUrl($descriptor['targetUrl']);
//    }
//
//    protected function validateCustomAd(AdTagInterface $adTag)
//    {
//         if (null === $adTag->getHtml()) {
//             throw new InvalidFormException('expect html of ad tag');
//         }
//    }
//
//    /**
//     * validate ImageUrl.
//     * @param $imageUrl
//     */
//    protected function validateImageUrl($imageUrl)
//    {
//        if (null === $imageUrl || sizeof($imageUrl) < 0) {
//            throw new InvalidFormException('The descriptor for AD_TYPE_IMAGE invalid: \'imageUrl\' must not null"', $this);
//        }
//
//        $this->validateUrl($imageUrl);
//    }
//
//    /**
//     * validate TargetUrl
//     * @param $targetUrl
//     */
//    protected function validateTargetUrl($targetUrl)
//    {
//        $this->validateUrl($targetUrl);
//    }
//
//    /**
//     * validate Url format
//     * @param $url
//     */
//    protected function validateUrl($url)
//    {
//        if(!filter_var($url, FILTER_VALIDATE_URL)){
//            throw new InvalidFormException('The format of url "%url%" is invalid.', $this);
//        }
//    }

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