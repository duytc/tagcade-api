<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
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

        if ($this->userRole instanceof AdminInterface) {
            $builder->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot */
                $libraryDisplayAdSlot = $event->getData();

                // check if missing "publisher" when using admin account. TODO: this should be validated in Entity.Core.LibraryAdSlotAbstract.yml instead of here!
                if ($this->userRole instanceof AdminInterface) {
                    if ($libraryDisplayAdSlot->getPublisher() === null) {
                        $event->getForm()->get('publisher')->addError(new FormError('publisher must not be null'));
                        return;
                    }
                } else if ($this->userRole instanceof PublisherInterface) {
                    if ($libraryDisplayAdSlot->getPublisher() === null) {
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