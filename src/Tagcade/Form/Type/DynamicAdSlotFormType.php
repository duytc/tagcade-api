<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class DynamicAdSlotFormType extends AbstractRoleSpecificFormType
{
    /** @var DisplayAdSlotRepositoryInterface */
    private $displayAdSlotRepository;

    /** @var SiteRepositoryInterface */
    private $siteRepository;

    function __construct(DisplayAdSlotRepositoryInterface $displayAdSlotRepository, SiteRepositoryInterface $siteRepository)
    {
        $this->displayAdSlotRepository = $displayAdSlotRepository;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // allow all sites, default is fine
            $builder->add('site', 'entity', array(
                    'class' => Site::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('site')->select('site'); }

            ))
            ->add('defaultAdSlot', 'entity', array(
                'class' => AdSlotAbstract::class,
                'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('adslot')->select('adslot'); }
            ))
            ;

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
                ])
                ->add('defaultAdSlot', 'entity', array(
                    'class' => AdSlotAbstract::class,
                    'query_builder' => function (AdSlotRepositoryInterface $er) {
                        /** @var PublisherInterface $publisher */
                        $publisher = $this->userRole;
                        return $er->getAdSlotsForPublisherQuery($publisher);
                    }
                ))
            ;

        } else {
            throw new LogicException('A valid user role is required by AdSlotFormType');
        }

        $builder
            ->add('libraryAdSlot', 'entity', array('class' => LibraryDynamicAdSlot::class))
            ->add('expressions', 'collection',  array(
                    'mapped' => true,
                    'type' => new ExpressionFormType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                )
            )
        ;


        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $dynamicAdSlot = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdSlot', $dynamicAdSlot) && is_array($dynamicAdSlot['libraryAdSlot'])){
                    $form->remove('libraryAdSlot');
                    $form->add('libraryAdSlot', new LibraryDynamicAdSlotFormType($this->userRole));

                    if($this->userRole instanceof AdminInterface) {
                        $site = $this->siteRepository->find($dynamicAdSlot['site']);
                        if(!$site instanceof SiteInterface) {
                            $form->get('site')->addError(new FormError('This value is not valid'));
                            return;
                        }

                        $dynamicAdSlot['libraryAdSlot']['publisher'] = $site->getPublisher()->getId();
                        $event->setData($dynamicAdSlot);
                    }
                }
            }
        );


        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var DynamicAdSlotInterface $dynamicAdSlot */
                $dynamicAdSlot = $event->getData();
                /** @var LibraryDynamicAdSlotInterface $libraryAdSlot */
                $libraryAdSlot = $dynamicAdSlot->getLibraryAdSlot();

                if($libraryAdSlot === null) {
                    // return here to let the Validation rules add error to form
                    return;
                }

                $libraryExpressions = $libraryAdSlot->getLibraryExpressions();

                if(($libraryExpressions === null || ($libraryExpressions instanceof PersistentCollection && count($libraryExpressions) < 1))
                    && $dynamicAdSlot->getDefaultAdSlot() === null) {
                    $event->getForm()->addError(new FormError("DefaultAdSlot and LibraryExpressions can not be both null"));
                    return;
                }

                if($dynamicAdSlot->getSite() instanceof SiteInterface &&
                    $dynamicAdSlot->getDefaultAdSlot() instanceof BaseAdSlotInterface &&
                    $dynamicAdSlot->getDefaultAdSlot()->getSite()->getId() != $dynamicAdSlot->getSite()->getId()) {
                    throw new InvalidArgumentException('DynamicAdSlot and DefaultAdSlot do not belong to the same site');
                }

                // if we create new Dynamic AdSlot from existing Library Dynamic AdSlot
                // then this form must have child 'expression'
                if($libraryAdSlot->isVisible() && $libraryAdSlot->getId() !== null) {
                    $expressions = $event->getForm()->get('expressions')->getData();
                    /** @var ExpressionInterface $expression */
                    foreach($expressions as $expression) {
                        $expression->setDynamicAdSlot($dynamicAdSlot);
                    }
                } else { // we create new Dynamic AdSlot from scratch
                    $libraryExpressions = $libraryAdSlot->getLibraryExpressions();

                    if($libraryExpressions === null || is_string($libraryExpressions)) {
                        return;
                    }

                    /** @var LibraryExpressionInterface $libraryExpression */
                    foreach ($libraryExpressions as $libraryExpression) {
                        $expressions = $libraryExpression->getExpressions();
                        /** @var ExpressionInterface $expression */
                        foreach ($expressions as $expression) {
                            if(!($expression instanceof ExpressionInterface)) {
                                $event->getForm()->addError(new FormError("Expression null or not is array"));
                                return;
                            }

                            if($dynamicAdSlot->getSite() instanceof SiteInterface &&
                                $expression->getExpectAdSlot()->getSite()->getId() != $dynamicAdSlot->getSite()->getId()) {
                                throw new InvalidArgumentException('DynamicAdSlot and ExpectAdSlot do not belong to the same site');
                            }

                            $expression->setDynamicAdSlot($dynamicAdSlot);
                        }
                    }
                }

                if(!$dynamicAdSlot->isSupportedNative()) {
                    // Validate defaultAdSlot for native selected
                    if($dynamicAdSlot->getDefaultAdSlot() instanceof NativeAdSlotInterface) {
                        $event->getForm()->get('defaultAdSlot')->addError(new FormError('DefaultAdSlot must be only DisplayAdSlot in case of DynamicAdSlot\'s native not supported!'));

                        return;
                    }
                }

                // implicitly set publisher
                $site = $dynamicAdSlot->getSite();
                if($site instanceof SiteInterface) {
                    $publisher = $site->getPublisher();

                    if($libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
                        $libraryAdSlot->setPublisher($publisher);
                        $dynamicAdSlot->setLibraryAdSlot($libraryAdSlot);
                    }
                }
            }
        );
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DynamicAdSlot::class,
                'cascade_validation' => true,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_dynamic_ad_slot';
    }
}