<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Model\Core\LibrarySlotTagInterface;

class LibrarySlotTagFormType extends AbstractRoleSpecificFormType
{
    // temporarily store $autoIncreasePosition value get from submitted form
    protected $autoIncreasePosition = false;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position')
            ->add('active')
            ->add('frequencyCap')
            ->add('rotation')
            ->add('libraryAdTag', 'entity', array(
                    'class' => LibraryAdTag::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('libTag')->select('libTag'); }
                ))
            ->add('libraryAdSlot', 'entity', array (
                    'class' => LibraryAdSlotAbstract::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('libSlot')->select('libSlot'); }
                )
            )
            ->add('refId')
            ->add('autoIncreasePosition', null, array('mapped' => false))
            ->add('impressionCap')
            ->add('networkOpportunityCap')
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

                if (array_key_exists('autoIncreasePosition', $librarySlotTag)) {
                    $this->autoIncreasePosition = $librarySlotTag['autoIncreasePosition'];
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var LibrarySlotTagInterface $librarySlotTag */
                $librarySlotTag = $event->getData();

                // explicitly add LibrarySlotTag to library ad slot when creating new library ad tag
                if ($librarySlotTag->getId() === null) {
                    $libraryAdSlot = $librarySlotTag->getLibraryAdSlot();
                    $libraryAdSlot->addLibSlotTag($librarySlotTag);
                }

                if ($this->autoIncreasePosition == true) {
                    // reset var $autoIncreasePosition
                    $this->autoIncreasePosition = false;

                    if ($librarySlotTag->getPosition() != null) {
                        // temporarily set AutoIncreasePosition field to ad tag for using when saving ad tag
                        $librarySlotTag->setAutoIncreasePosition(true);
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
                'data_class' => LibrarySlotTag::class,
                'cascade_validation' => true,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_library_slot_tag';
    }
}