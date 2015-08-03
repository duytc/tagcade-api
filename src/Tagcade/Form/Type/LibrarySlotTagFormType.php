<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibrarySlotTag;

class LibrarySlotTagFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position')
            ->add('active')
            ->add('frequencyCap')
            ->add('rotation')
            ->add('libraryAdTag', 'entity', array('class' => LibraryAdTag::class))
            ->add('libraryAdSlot')
            ->add('refId')
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $librarySlotTag = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdTag', $librarySlotTag) && is_array($librarySlotTag['libraryAdTag'])){
                    $form->remove('libraryAdTag');
                    $form->add('libraryAdTag', new LibraryAdTagFormType($this->userRole));
                }
            }
        );
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => LibrarySlotTag::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_library_slot_tag';
    }
}