<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryNativeAdSlot;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;

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
            ->add('name')
            ->add('visible')
            ->add('id')
            ->add('buyPrice');

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
                /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
                $libraryNativeAdSlot = $event->getData();

                // check if missing "publisher" when using admin account. TODO: this should be validated in Entity.Core.LibraryAdSlotAbstract.yml instead of here!
                if ($this->userRole instanceof AdminInterface) {
                    if ($libraryNativeAdSlot->getPublisher() === null) {
                        $event->getForm()->get('publisher')->addError(new FormError('publisher must not be null'));
                        return;
                    }
                } else if ($this->userRole instanceof PublisherInterface) {
                    if ($libraryNativeAdSlot->getPublisher() === null) {
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
                'allow_extra_fields' => true,
                'data_class' => LibraryNativeAdSlot::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_library_native_ad_slot';
    }
}