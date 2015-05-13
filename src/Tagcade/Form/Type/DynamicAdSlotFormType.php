<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;
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

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /**
                 * @var DynamicAdSlotInterface $dynamicAdSlot
                 */
                $dynamicAdSlot = $event->getForm()->getData();

//                if (null === $event->getForm()->get('expressions')
//                ) {
//                    $form = $event->getForm();
//
//                    $form->get(ExpressionFormType::KEY_EXPRESSIONS)->addError(new FormError('Expressions is null! Expect not null && is array'));
//
//                    return;
//                }

                $expressions = $event->getForm()->get('expressions')->getData();
//                if (null === $expressions
//                    || !is_array($expressions)
//                    || count($expressions) < 1
//                ) {
//                    $form = $event->getForm();
//
//                    $form->get(ExpressionFormType::KEY_EXPRESSIONS)->addError(new FormError('Expressions not is array! Expect not null && is array'));
//
//                    return;
//                }


                try {
                    if (null != $expressions && is_array($expressions) ) {
                        $this->updateDynamicAdSlotForExpression($dynamicAdSlot, $expressions);
                    }

                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();

                    $form->get(ExpressionFormType::KEY_EXPRESSION_DESCRIPTOR)->addError(new FormError($ex->getMessage()));
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
        return 'tagcade_form_ad_slot';
    }
}