<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryNativeAdSlot;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryNativeAdSlotFormType extends AbstractRoleSpecificFormType
{
    protected $userRole;
    
    function __construct($userRole = null)
    {
        $this->userRole = $userRole;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenceName')
            ->add('visible')
            ->add('id')
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                if($this->userRole instanceof PublisherInterface){
                    /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
                    $libraryNativeAdSlot = $event->getData();

                    if($libraryNativeAdSlot->getPublisher() === null)
                    {
                        $libraryNativeAdSlot->setPublisher($this->userRole);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => LibraryNativeAdSlot::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_library_native_ad_slot';
    }
}