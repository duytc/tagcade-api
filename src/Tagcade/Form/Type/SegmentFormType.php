<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Segment;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SegmentFormType extends AbstractRoleSpecificFormType
{
    protected $userRole;

    function __construct($userRole = null)
    {
        $this->userRole = $userRole;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');

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
                /** SegmentInterface $segment */
                $segment = $event->getData();

                if ($this->userRole instanceof AdminInterface) {
                    if ($segment->getPublisher() === null) {
                        $event->getForm()->get('publisher')->addError(new FormError('publisher must not be null'));
                        return;
                    }
                } else if ($this->userRole instanceof PublisherInterface) {
                    if ($segment->getPublisher() === null) {
                        $segment->setPublisher($this->userRole);
                    }
                }

            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Segment::class
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_segment';
    }
}