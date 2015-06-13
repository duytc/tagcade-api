<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\NativeAdSlot;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class DynamicAdSlotFormType extends AbstractRoleSpecificFormType
{

    /** @var AdSlotRepositoryInterface */
    private $repository;

    function __construct(AdSlotRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // allow all sites, default is fine
            $builder->add('site');

        } else if ($this->userRole instanceof PublisherInterface) {

            // for publishers, only allow their sites
            $builder
                ->add('site', 'entity', [
                    'class' => Site::class,
                    'query_builder' => function (SiteRepositoryInterface $repository) {
                        /** @var PublisherInterface $publisher */
                        $publisher = $this->userRole;

                        return $repository->getSitesForPublisherQuery($publisher);
                    }
                ]);

        } else {
            throw new LogicException('A valid user role is required by AdSlotFormType');
        }

        $builder
            ->add('name')
            ->add('defaultAdSlot')
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
                /** @var null|DynamicAdSlotInterface $dynamicAdSlot */
                $dynamicAdSlot = $event->getData();
                $form = $event->getForm();

                // check if the DynamicAdSlot object is "new"
                // If no data is passed to the form, the data is "null".
                // This should be considered a new "DynamicAdSlot"
                if (!$dynamicAdSlot || null === $dynamicAdSlot->getId()) {
                    $form->add('native');
                }
        });

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                // Validate expressions and update for dynamic adSlot
                /** @var DynamicAdSlotInterface $dynamicAdSlot */
                $dynamicAdSlot = $event->getForm()->getData();

                /** @var null|ExpressionInterface[] $expressions */
                $expressions = $event->getForm()->get('expressions')->getData();

                try {
                    // remove last expressions
                    $dynamicAdSlot->getExpressions()->clear();
                    if (null != $expressions && is_array($expressions) ) {
                        $this->updateDynamicAdSlotForExpression($dynamicAdSlot, $expressions);
                    }

                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();

                    $form->get('expressions')->addError(new FormError($ex->getMessage()));
                }

                // Validate defaultAdSlot and expectedAdSlot for native selected
                if(!($dynamicAdSlot->isSupportedNative())) {
                    // Validate defaultAdSlot for native selected
                    if($dynamicAdSlot->getDefaultAdSlot() instanceof NativeAdSlot) {
                        $form = $event->getForm();

                        $form->get('defaultAdSlot')->addError(new FormError('DefaultAdSlot must be only DisplayAdSlot in case of DynamicAdSlot\'s native is false!'));

                        return;
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
            }
        );
    }

    /**
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @param ExpressionInterface[] $persistingExpressions
     */
    protected function updateDynamicAdSlotForExpression(DynamicAdSlotInterface $dynamicAdSlot, array $persistingExpressions)
    {
        $currentExpressions = $dynamicAdSlot->getExpressions()->toArray();
        $totalCurrentExpression = count($currentExpressions);
        // remove old expressions
        for($i = $totalCurrentExpression-1; $i >= 0; $i -- ) {
            $dynamicAdSlot->getExpressions()->remove($i);
        }

        // creating new expressions
        foreach ($persistingExpressions as $expression) {
            $expression->setDynamicAdSlot($dynamicAdSlot);

            $dynamicAdSlot->getExpressions()->add($expression);
        }

    }



    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DynamicAdSlot::class,
            ]);
    }

    public function getName()
    {
        //return 'tagcade_form_ad_slot';
        return 'tagcade_form_dynamic_ad_slot';
    }
}