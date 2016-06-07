<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Repository\Core\AdNetworkPartnerRepositoryInterface;

class AdNetworkFormType extends AbstractRoleSpecificFormType
{
    /** @var AdNetworkPartnerRepositoryInterface */
    private $adNetworkPartnerRepository;

    function __construct(AdNetworkPartnerRepositoryInterface $adNetworkPartnerRepository)
    {
        $this->adNetworkPartnerRepository = $adNetworkPartnerRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('url')
            ->add('defaultCpmRate')
            ->add('username')
            ->add('password')
            ->add('impressionCap')
            ->add('emailHookToken')
            ->add('networkOpportunityCap')
            ->add('networkPartner', 'entity', array(
                'class' => AdNetworkPartner::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('np')->select('np');
                }
            ));;

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
                /** @var AdNetworkInterface $adNetwork */
                $adNetwork = $event->getData();
                // initialize adTags count when creating new ad network;
                if ($adNetwork->getId() === null) {
                    $adNetwork->setActiveAdTagsCount(0);
                    $adNetwork->setPausedAdTagsCount(0);
                }

                if (!empty($adNetwork->getName()) || !empty($adNetwork->getUrl())) { // this is custom ad network
                    $adNetwork->setNetworkPartner(null); // remove built-in network partner
                }

                $networkPartner = $adNetwork->getNetworkPartner();
                if ($networkPartner instanceof AdNetworkPartnerInterface) {
                    $adNetwork->setName($networkPartner->getName());
                    $adNetwork->setUrl($networkPartner->getUrl());
                }

                $adNetworkName = $adNetwork->getName();
                if (empty($adNetworkName) || strlen($adNetworkName) < 2) {
                    $event->getForm()->addError(new FormError('name should not be blank and must be more than two characters'));
                }

                if ($adNetwork->getDefaultCpmRate() < 0) {
                    $event->getForm()->addError(new FormError('defaultCpmRate is either zero or positive numeric value'));
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AdNetwork::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ad_network';
    }
}