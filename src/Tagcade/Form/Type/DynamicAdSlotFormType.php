<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class DynamicAdSlotFormType extends AbstractRoleSpecificFormType
{
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
            ->add('libraryAdSlot', 'entity', array('class' => LibraryDynamicAdSlot::class))
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
                }
            }
        );


        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                // Validate expressions and update for dynamic adSlot
                /** @var DynamicAdSlotInterface $dynamicAdSlot */
                $dynamicAdSlot = $event->getForm()->getData();

                $site = $dynamicAdSlot->getSite();
                $publisher = $site->getPublisher();

                // set dynamicAdSlotLib to DynamicAdSlot for cascade persist
                /** @var LibraryDynamicAdSlotInterface $libraryDisplayAdSlot */
                $libraryDisplayAdSlot = $event->getForm()->get('libraryAdSlot')->getData();
                $libraryDisplayAdSlot->setPublisher($publisher);

                $dynamicAdSlot->setLibraryAdSlot($libraryDisplayAdSlot);
            }
        );
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
        return 'tagcade_form_dynamic_ad_slot';
    }
}