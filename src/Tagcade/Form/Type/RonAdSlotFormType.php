<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Entity\Core\RonAdSlot;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotSegmentInterface;
use Tagcade\Model\RTBEnabledInterface as RTB_STATUS;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class RonAdSlotFormType extends AbstractRoleSpecificFormType
{
    /** @var null|BaseLibraryAdSlotInterface */
    private $oldLibraryAdSlot = null;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publisher', null, array('mapped' => false))
            ->add('libraryAdSlot', 'entity', array(
                'class' => LibraryAdSlotAbstract::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('libSlot')->select('libSlot');
                }
            ))
            ->add('ronAdSlotSegments', 'collection', array(
                'mapped' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'type' => new RonAdSlotSegmentFormType($this->userRole)
            ))
            ->add('floorPrice')
            ->add('rtbStatus', ChoiceType::class, array(
                'choices' => array(
                    RTB_STATUS::RTB_ENABLED,
                    RTB_STATUS::RTB_DISABLED
                )
            ));

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $ronAdSlot = $event->getData();

                if ($this->userRole instanceof AdminInterface) {
                    if (!array_key_exists('publisher', $ronAdSlot)) {
                        $form->get('publisher')->addError(new FormError('expect a PublisherInterface object'));
                        return;
                    }
                    $publisher = $ronAdSlot['publisher'];
                    $libraryAdSlot = $ronAdSlot['libraryAdSlot'];
                    // $libraryAdSlot can be just an id of existing library
                    if (is_array($libraryAdSlot)) {
                        $libraryAdSlot['publisher'] = $publisher;
                    }

                    $ronAdSlotSegments = $ronAdSlot['ronAdSlotSegments'];
                    if ($ronAdSlotSegments !== null && !is_array($ronAdSlotSegments)) {
                        $form->get('ronAdSlotSegments')->addError(new FormError('expect a array object or null'));
                        return;
                    }

                    if ($ronAdSlotSegments === null) {
                        $form->remove('ronAdSlotSegments');
                    } else {
                        foreach ($ronAdSlotSegments as &$ronAdSlotSegment) {
                            // $ronAdSlotSegment can be just an id of existing segment
                            if (is_array($ronAdSlotSegment['segment'])) {
                                $ronAdSlotSegment['segment']['publisher'] = $publisher;
                            }
                        }
                        $ronAdSlot['ronAdSlotSegments'] = $ronAdSlotSegments;
                    }

                    $ronAdSlot['libraryAdSlot'] = $libraryAdSlot;
                    $event->setData($ronAdSlot);
                }
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                $ronAdSlot = $event->getData();
                // if we are updating a ron ad slot
                if ($ronAdSlot instanceof RonAdSlotInterface && $ronAdSlot->getId() !== null) {
                    $this->oldLibraryAdSlot = $ronAdSlot->getLibraryAdSlot();
                }

                // validate rtbStatus before submitting
                if ($this->userRole instanceof PublisherInterface && !$this->userRole->hasRtbModule()) {
                    if ($form->has('rtbStatus') && $form->get('rtbStatus')->getData() !== null) {
                        $form->get('rtbStatus')->addError(new FormError('this site belongs to publisher that does not have rtb module enabled'));
                        return;
                    }
                }
            });

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /**@var RonAdSlotInterface $ronAdSlot */
                $ronAdSlot = $event->getData();

                $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();
                if ($libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
                    if ($libraryAdSlot->getRonAdSlot() instanceof RonAdSlotInterface && $ronAdSlot->getId() === null) {
                        throw new LogicException('the library ad slot had been referred by another ron ad slot');
                    }

                    if ($this->oldLibraryAdSlot !== null && $this->oldLibraryAdSlot->getId() !== $libraryAdSlot->getId()) {
                        throw new LogicException('A ron ad slot can not change its library ad slot once created');
                    }

                    $libraryAdSlot->setRonAdSlot($ronAdSlot);
                    //make $libraryAdSlot visible to force AdSlotGenerator service replicating AdTag also
                    $libraryAdSlot->setVisible(true);
                }

                $ronAdSlotSegments = $ronAdSlot->getRonAdSlotSegments()->toArray();

                if (is_array($ronAdSlotSegments)) {
                    /** @var RonAdSlotSegmentInterface $ronAdSlotSegment */
                    foreach ($ronAdSlotSegments as $ronAdSlotSegment) {
                        $ronAdSlotSegment->setRonAdSlot($ronAdSlot);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => RonAdSlot::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ron_ad_slot';
    }
}