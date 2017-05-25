<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\DomainManager\NetworkBlacklistManagerInterface;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\Core\NetworkWhiteListInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Repository\Core\AdNetworkPartnerRepositoryInterface;

class AdNetworkFormType extends AbstractRoleSpecificFormType
{
    /** @var AdNetworkPartnerRepositoryInterface */
    private $adNetworkPartnerRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NetworkBlacklistManagerInterface $networkBlacklistManager
     */
    private $networkBlacklistManager;

    function __construct(AdNetworkPartnerRepositoryInterface $adNetworkPartnerRepository, ObjectManager $om, NetworkBlacklistManagerInterface $networkBlacklistManager)
    {
        $this->adNetworkPartnerRepository = $adNetworkPartnerRepository;
        $this->em = $om;
        $this->networkBlacklistManager = $networkBlacklistManager;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('url')
            ->add('defaultCpmRate')
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

        $builder->add('networkBlacklists', 'collection', array(
                'mapped' => true,
                'type' => new NetworkBlacklistFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );
        /* custom impression pixel */
        $builder->add('customImpressionPixels');

        $builder->add('networkWhiteLists', 'collection', array(
                'mapped' => true,
                'type' => new NetworkWhiteListFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var AdNetworkInterface $adNetwork */
                $adNetwork = $event->getData();
                $form = $event->getForm();

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

                /** @var Collection| NetworkBlacklistInterface[] $networkBlacklists */
                $networkBlacklists = $event->getForm()->get('networkBlacklists')->getData();

                /** @var Collection| NetworkWhiteListInterface[] $networkWhiteLists */
                $networkWhiteLists = $event->getForm()->get('networkWhiteLists')->getData();

                if ($networkBlacklists === null) {
                    $form->get('networkBlacklists')->addError(new FormError('networkBlacklists must be an array string'));
                    return;
                }

                if ($networkWhiteLists === null) {
                    $form->get('$networkWhiteLists')->addError(new FormError('$networkWhiteLists must be an array string'));
                    return;
                }

                if (count($networkWhiteLists) > 0 && count($networkBlacklists) > 0) {
                    throw new InvalidArgumentException('Demand partner can not have both blacklist and white list');
                }

                foreach ($networkBlacklists as $networkBlacklist) {
                    if (!$networkBlacklist->getAdNetwork() instanceof AdNetworkInterface) {
                        $networkBlacklist->setAdNetwork($adNetwork);
                    }
                }

                if ($networkBlacklists instanceof Collection) {
                    $networkBlacklists = $networkBlacklists->toArray();
                }

                $adNetwork->setNetworkBlacklists($networkBlacklists);

                foreach ($networkWhiteLists as $networkWhiteList) {
                    if (!$networkWhiteList->getAdNetwork() instanceof AdNetworkInterface) {
                        $networkWhiteList->setAdNetwork($adNetwork);
                    }
                }

                if ($networkWhiteLists instanceof Collection) {
                    $networkWhiteLists = $networkWhiteLists->toArray();
                }

                $adNetwork->setNetworkWhiteLists($networkWhiteLists);

                /* custom Impress Pixels */
                $customImpressionPixels = $adNetwork->getCustomImpressionPixels();
                if (!is_array($customImpressionPixels) && null !== $customImpressionPixels) {
                    $form->get('customImpressionPixels')->addError(new FormError('customImpressionPixels must be an array or null'));
                }

                $adNetwork->setCustomImpressionPixels($customImpressionPixels);

            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => AdNetwork::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ad_network';
    }
}