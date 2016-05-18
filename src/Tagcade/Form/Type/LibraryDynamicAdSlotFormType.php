<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\LibraryAdSlotRepositoryInterface;

class LibraryDynamicAdSlotFormType extends AbstractRoleSpecificFormType
{
    protected $userRole;

    function __construct($userRole = null)
    {
        if ($userRole instanceof UserRoleInterface) {
            $this->userRole = $userRole;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('visible')
            ->add('id')
            ->add('libraryExpressions', 'collection', array(
                    'mapped' => true,
                    'type' => new LibraryExpressionFormType(new ExpressionInJsGenerator()),
                    'allow_add' => true,
                    'allow_delete' => true,
                )
            );

        if ($this->userRole instanceof AdminInterface) {
            $builder
                ->add(
                    $builder->create('publisher')
                        ->addModelTransformer(
                            new RoleToUserEntityTransformer(), false
                        )
                )
                ->add('defaultLibraryAdSlot', 'entity', array(
                    'class' => LibraryAdSlotAbstract::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('libadslot')->select('libadslot');
                    }
                ));
        } else if ($this->userRole instanceof PublisherInterface) {
            $builder
                ->add('defaultLibraryAdSlot', 'entity', array(
                    'class' => LibraryAdSlotAbstract::class,
                    'query_builder' => function (LibraryAdSlotRepositoryInterface $er) {
                        /** @var PublisherInterface $publisher */
                        $publisher = $this->userRole;

                        /*
                         * IMPORTANT: get all library ad slots, for a publisher, WITHOUT checking 'visible',
                         * because the defaultLibraryAdSlot may be not yet shared (the own defaultAdSlot belongs to only one site, not yet shared)!!!
                         */
                        return $er->getAllLibraryAdSlotsForPublisherQuery($publisher);
                    }
                ));
        }

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

        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $libraryDynamicAdSlot = $event->getData();

                if (array_key_exists('libraryExpressions', $libraryDynamicAdSlot)) {
                    $libraryExpressions = $libraryDynamicAdSlot['libraryExpressions'];

                    if (!is_array($libraryExpressions)) {
                        $form->remove('libraryExpressions');
                        $form->add('libraryExpressions', 'text');
                    }
                }
            });

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                // Validate expressions and update for dynamic adSlot
                /** @var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot */
                $libraryDynamicAdSlot = $event->getForm()->getData();
                /** @var null|LibraryExpressionInterface[] $libraryExpressions */
                $libraryExpressions = $event->getForm()->get('libraryExpressions')->getData();

                if ($libraryExpressions === null || is_string($libraryExpressions)) {
                    $form->get('libraryExpressions')->addError(new FormError('libraryExpressions must be an array string'));
                    return;
                }

                try {
                    foreach ($libraryExpressions as $libExp) {
                        $libExp->setLibraryDynamicAdSlot($libraryDynamicAdSlot);
                    }

                    if (count($libraryDynamicAdSlot->getLibraryExpressions()) < 1 && null === $libraryDynamicAdSlot->getDefaultLibraryAdSlot()) {
                        throw new InvalidFormException('expect expression or default ad slot');
                    }

                } catch (InvalidFormException $ex) {
                    $form->get('libraryExpressions')->addError(new FormError($ex->getMessage()));

                    return;
                }

                // Validate defaultAdSlot and expectedAdSlot for native selected
                if (!($libraryDynamicAdSlot->isSupportedNative())) {
                    // Validate defaultAdSlot for native selected
                    if ($libraryDynamicAdSlot->getDefaultLibraryAdSlot() instanceof LibraryNativeAdSlotInterface) {
                        $form->get('defaultLibraryAdSlot')->addError(new FormError('DefaultAdSlot must be only DisplayAdSlot in case of DynamicAdSlot\'s native not supported!'));

                        return;
                    }

                    if (null === $libraryExpressions) {
                        return; // ignore if expression is null
                    }
                    // Validate expectedAdSlot for native selected
                    foreach ($libraryExpressions as $idx => $libraryExpression) {
                        if ($libraryExpression->getExpectLibraryAdSlot() instanceof LibraryNativeAdSlotInterface) {
                            $form->get('libraryExpressions')[$idx]->get('expectLibraryAdSlot')->addError(new FormError('ExpectedAdSlot must be only DisplayAdSlot in case of DynamicAdSlot\'s native not supported!'));

                            return;
                        }
                    }
                }

                // check if missing "publisher" when using admin account. TODO: this should be validated in Entity.Core.LibraryAdSlotAbstract.yml instead of here!
                if ($this->userRole instanceof AdminInterface) {
                    if ($libraryDynamicAdSlot->getPublisher() === null) {
                        $event->getForm()->get('publisher')->addError(new FormError('publisher must not be null'));
                        return;
                    }
                } else if ($this->userRole instanceof PublisherInterface) {
                    if ($libraryDynamicAdSlot->getPublisher() === null) {
                        $libraryDynamicAdSlot->setPublisher($this->userRole);
                    }
                }

            }
        );
    }

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param Collection $persistingLibraryExpressions
     */
    protected function updateLibraryDynamicAdSlotForExpression(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, Collection $persistingLibraryExpressions)
    {
        $currentLibraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions()->toArray();
        $totalCurrentLibraryExpression = count($currentLibraryExpressions);
        // remove old expressions
        for ($i = $totalCurrentLibraryExpression - 1; $i >= 0; $i--) {
            $libraryDynamicAdSlot->getLibraryExpressions()->remove($i);
        }

        // creating new expressions
        /** @var LibraryExpressionInterface $libraryExpression */
        foreach ($persistingLibraryExpressions as $libraryExpression) {
            $libraryExpression->setLibraryDynamicAdSlot($libraryDynamicAdSlot);

            $libraryDynamicAdSlot->getLibraryExpressions()->add($libraryExpression);
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
        return 'tagcade_form_library_dynamic_ad_slot';
    }
}