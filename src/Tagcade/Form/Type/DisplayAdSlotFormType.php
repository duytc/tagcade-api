<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryDisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class DisplayAdSlotFormType extends AbstractRoleSpecificFormType
{
    /** @var DisplayAdSlotRepositoryInterface */
    private $adSlotRepository;

    /** @var SiteRepositoryInterface */
    private $siteRepository;

    function __construct(DisplayAdSlotRepositoryInterface $adSlotRepository, SiteRepositoryInterface $siteRepository)
    {
        $this->adSlotRepository = $adSlotRepository;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hbBidPrice')
            ->add('autoRefresh')
            ->add('autoOptimize')
            ->add('refreshEvery')
            ->add('maximumRefreshTimes');

        if ($this->userRole instanceof AdminInterface) {

            // allow all sites, default is fine
            $builder->add('site', 'entity', array(
                    'class' => Site::class,
                    'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('site')->select('site'); }
                )
            )
            ->add('libraryAdSlot', 'entity', array(
                'class' => LibraryDisplayAdSlot::class,
                'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('slot')->select('slot'); }
            ));

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
                ->add('libraryAdSlot', 'entity', array(
                    'class' => LibraryDisplayAdSlot::class,
                    'query_builder' => function (LibraryDisplayAdSlotRepositoryInterface $er) {
                        /** @var PublisherInterface $publisher */
                        $publisher = $this->userRole;
                        return $er->getAllLibraryDisplayAdSlotsForPublisherQuery($publisher);
                    }
                ));

        } else {
            throw new LogicException('A valid user role is required by AdSlotFormType');
        }

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                // validate headerBidding before submitting
                if($this->userRole instanceof PublisherInterface && !$this->userRole->hasHeaderBiddingModule()) {

                    if($form->has('headerBiddingPrice') && $form->get('headerBiddingPrice')->getData() !=null) {
                        $form->get('headerBiddingPrice')->addError(new FormError('This publisher does not set header bidding module'));
                        return;
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $displayAdSlot = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdSlot', $displayAdSlot) && is_array($displayAdSlot['libraryAdSlot'])){
                    $form->remove('libraryAdSlot');
                    $form->add('libraryAdSlot', new LibraryAdSlotFormType($this->userRole));

                    if($this->userRole instanceof AdminInterface) {
                        $site = $this->siteRepository->find($displayAdSlot['site']);
                        if(!$site instanceof SiteInterface) {
                            $form->get('site')->addError(new FormError('This value is not valid'));
                            return;
                        }

                        $displayAdSlot['libraryAdSlot']['publisher'] = $site->getPublisher()->getId();
                        $event->setData($displayAdSlot);
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
                'data_class' => DisplayAdSlot::class,
                'cascade_validation' => true,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ad_slot';
    }
}