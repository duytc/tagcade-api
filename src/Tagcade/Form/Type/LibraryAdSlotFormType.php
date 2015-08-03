<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryAdSlotFormType extends AbstractRoleSpecificFormType
{
    protected $userRole;

    function __construct($userRole = null)
    {
        if($userRole instanceof UserRoleInterface)  $this->userRole = $userRole;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('width')
            ->add('height')
            ->add('visible')
            ->add('id')
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                if($this->userRole instanceof PublisherInterface){
                    /** @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot */
                    $libraryDisplayAdSlot = $event->getData();

                    if($libraryDisplayAdSlot->getPublisher() === null)
                    {
                        $libraryDisplayAdSlot->setPublisher($this->userRole);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => LibraryDisplayAdSlot::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_library_ad_slot';
    }
}