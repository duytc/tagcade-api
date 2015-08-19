<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\LibraryNativeAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlot;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\NativeAdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class NativeAdSlotFormType extends AbstractRoleSpecificFormType
{
    /** @var NativeAdSlotRepositoryInterface */
    private $nativeAdSlotRepository;

    /** @var SiteRepositoryInterface */
    private $siteRepository;

    function __construct(NativeAdSlotRepositoryInterface $nativeAdSlotRepository, SiteRepositoryInterface $siteRepository)
    {
        $this->nativeAdSlotRepository = $nativeAdSlotRepository;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // allow all sites, default is fine
            $builder->add('site', 'entity', ['class' => Site::class]);

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
            throw new LogicException('A valid user role is required by NativeAdSlotFormType');
        }

        $builder
            ->add('libraryAdSlot', 'entity', array('class' => LibraryNativeAdSlot::class))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $nativeAdSlot = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdSlot', $nativeAdSlot) && is_array($nativeAdSlot['libraryAdSlot'])){
                    $form->remove('libraryAdSlot');
                    $form->add('libraryAdSlot', new LibraryNativeAdSlotFormType($this->userRole));

                    if($this->userRole instanceof AdminInterface) {
                        $site = $this->siteRepository->find($nativeAdSlot['site']);
                        if(!$site instanceof SiteInterface) {
                            $form->get('site')->addError(new FormError('This value is not valid'));
                            return;
                        }

                        $nativeAdSlot['libraryAdSlot']['publisher'] = $site->getPublisher()->getId();
                        $event->setData($nativeAdSlot);
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var NativeAdSlotInterface $nativeAdSlot */
                $nativeAdSlot = $event->getData();

                $site = $nativeAdSlot->getSite();
                if($site instanceof SiteInterface) {
                    $publisher = $site->getPublisher();

                    // set nativeAdSlotLib to NativeAdSlot for cascade persist
                    /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
                    $libraryNativeAdSlot = $event->getForm()->get('libraryAdSlot')->getData();
                    if($libraryNativeAdSlot instanceof LibraryNativeAdSlotInterface) {
                        $libraryNativeAdSlot->setPublisher($publisher);
                        $nativeAdSlot->setLibraryAdSlot($libraryNativeAdSlot);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => NativeAdSlot::class,
                'cascade_validation' => true,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_native_ad_slot';
    }
}