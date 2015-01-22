<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\AdSlot;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Model\User\Role\AdminInterface;

class AdTagFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // admins can add adSlots for any publisher
            $builder->add('adSlot');
            $builder->add('adNetwork');

        } else if ($this->userRole instanceof PublisherInterface) {

            /** @var PublisherInterface $publisher */
            $publisher = $this->userRole;

            $builder
                ->add('adSlot', 'entity', [
                    'class' => AdSlot::class,
                    'query_builder' => function(AdSlotRepositoryInterface $repository) use($publisher) {
                        return $repository->getAdSlotsForPublisherQuery($publisher);
                    }
                ])
                ->add('adNetwork', 'entity', [
                    'class' => AdNetwork::class,
                    'query_builder' => function(AdNetworkRepositoryInterface $repository) use ($publisher) {
                        return $repository->getAdNetworksForPublisherQuery($publisher);
                    }
                ])
            ;

            unset($publisher);

        } else {
            throw new LogicException('A valid user role is required by AdTagFormType');
        }

        $builder
            ->add('name')
            ->add('html')
            ->add('position')
            ->add('frequencyCap')
            ->add('active')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AdTag::class,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_ad_tag';
    }
}