<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class AdSlotFormType extends AbstractRoleSpecificFormType
{
    /** @var DisplayAdSlotRepositoryInterface */
    private $adSlotRepository;

    function __construct(DisplayAdSlotRepositoryInterface $adSlotRepository)
    {
        $this->adSlotRepository = $adSlotRepository;
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
            ->add('libraryAdSlot', 'entity', array('class' => LibraryDisplayAdSlot::class))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $displayAdSlot = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdSlot', $displayAdSlot) && is_array($displayAdSlot['libraryAdSlot'])){
                    $form->remove('libraryAdSlot');
                    $form->add('libraryAdSlot', new LibraryAdSlotFormType($this->userRole));
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var DisplayAdSlotInterface $displayAdSlot */
                $displayAdSlot = $event->getData();

                $site = $displayAdSlot->getSite();
                $publisher = $site->getPublisher();

                // set displayAdSlotLib to DisplayAdSlot for cascade persist
                /** @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot */
                $libraryDisplayAdSlot = $event->getForm()->get('libraryAdSlot')->getData();
                $libraryDisplayAdSlot->setPublisher($publisher);

                $displayAdSlot->setLibraryDisplayAdSlot($libraryDisplayAdSlot);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DisplayAdSlot::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ad_slot';
    }
}