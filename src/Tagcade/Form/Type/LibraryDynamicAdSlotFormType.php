<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlot;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryDynamicAdSlotFormType extends AbstractRoleSpecificFormType
{
    protected $userRole;

    function __construct($userRole = null)
    {
        if($userRole instanceof UserRoleInterface)
        {
            $this->userRole = $userRole;
        }
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenceName')
            ->add('defaultAdSlot')
            ->add('visible')
            ->add('id')
            ->add('expressions', 'collection',  array(
                    'mapped' => false,
                    'type' => new ExpressionFormType(),
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var null|LibraryDynamicAdSlotInterface $libraryDynamicAdSlot */
                $libraryDynamicAdSlot = $event->getData();
                $form = $event->getForm();

                // check if the DynamicAdSlot object is "new"
                // If no data is passed to the form, the data is "null".
                // This should be considered a new "DynamicAdSlot"
                if (!$libraryDynamicAdSlot || null === $libraryDynamicAdSlot->getId()) {
                    $form->add('native');
                }
        });

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                // Validate expressions and update for dynamic adSlot
                /** @var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot */
                $libraryDynamicAdSlot = $event->getForm()->getData();

                /** @var null|ExpressionInterface[] $expressions */
                $expressions = $event->getForm()->get('expressions')->getData();

                try {
                    // remove last expressions
                    if (null != $expressions && is_array($expressions) ) {
                        $libraryDynamicAdSlot->getExpressions()->clear();
                        $this->updateLibraryDynamicAdSlotForExpression($libraryDynamicAdSlot, $expressions);
                    }

                    if ($libraryDynamicAdSlot->getExpressions()->isEmpty()) {
                        throw new InvalidFormException('expect expression or default ad slot');
                    }

                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();
                    $form->get('expressions')->addError(new FormError($ex->getMessage()));

                    return;
                }

                // Validate defaultAdSlot and expectedAdSlot for native selected
                if(!($libraryDynamicAdSlot->isSupportedNative())) {
                    // Validate defaultAdSlot for native selected
                    if($libraryDynamicAdSlot->getDefaultAdSlot() instanceof NativeAdSlot) {
                        $form = $event->getForm();

                        $form->get('defaultAdSlot')->addError(new FormError('DefaultAdSlot must be only DisplayAdSlot in case of DynamicAdSlot\'s native is false!'));

                        return;
                    }

                    if (null === $expressions) {
                        return; // ignore if expression is null
                    }
                    // Validate expectedAdSlot for native selected
                    foreach ($expressions as $idx => $expression) {
                        if($expression->getExpectAdSlot() instanceof NativeAdSlot) {
                            $form = $event->getForm();

                            $form->get('expressions')[$idx]->get('expectAdSlot')->addError(new FormError('ExpectedAdSlot must be only DisplayAdSlot in case of DynamicAdSlot\'s native is false!'));

                            return;
                        }
                    }
                }

                if($this->userRole instanceof PublisherInterface){
                    if($libraryDynamicAdSlot->getPublisher() === null)
                    {
                        $libraryDynamicAdSlot->setPublisher($this->userRole);
                    }
                }
            }
        );
    }

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param ExpressionInterface[] $persistingExpressions
     */
    protected function updateLibraryDynamicAdSlotForExpression(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, array $persistingExpressions)
    {
        $currentExpressions = $libraryDynamicAdSlot->getExpressions()->toArray();
        $totalCurrentExpression = count($currentExpressions);
        // remove old expressions
        for($i = $totalCurrentExpression-1; $i >= 0; $i -- ) {
            $libraryDynamicAdSlot->getExpressions()->remove($i);
        }

        // creating new expressions
        foreach ($persistingExpressions as $expression) {
            $expression->setLibraryDynamicAdSlot($libraryDynamicAdSlot);

            $libraryDynamicAdSlot->getExpressions()->add($expression);
        }

    }



    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => LibraryDynamicAdSlot::class,
            ]);
    }

    public function getName()
    {
        //return 'tagcade_form_ad_slot';
        return 'tagcade_form_library_dynamic_ad_slot';
    }
}