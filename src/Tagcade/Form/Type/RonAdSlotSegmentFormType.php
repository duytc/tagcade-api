<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\RonAdSlotSegment;

class RonAdSlotSegmentFormType extends AbstractRoleSpecificFormType
{
    protected $userRole;

    function __construct($userRole = null)
    {
        $this->userRole = $userRole;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('segment')
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $ronAdSlotSegment = $event->getData();

                //create new segment
                if(array_key_exists('segment', $ronAdSlotSegment) && is_array($ronAdSlotSegment['segment'])){
                    $form->remove('segment');
                    $form->add('segment', new SegmentFormType($this->userRole));
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => RonAdSlotSegment::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ron_ad_slot_segment';
    }
}