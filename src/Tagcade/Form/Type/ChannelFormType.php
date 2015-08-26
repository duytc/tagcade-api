<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Channel;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\ChannelSiteInterface;
use Tagcade\Model\User\Role\AdminInterface;

class ChannelFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name');

        if ($this->userRole instanceof AdminInterface) {
            $builder->add(
                $builder->create('publisher')
                    ->addModelTransformer(
                        new RoleToUserEntityTransformer(), false
                    )
            );
        }

        $builder->add('channelSites', 'collection', array(
                'mapped' => true,
                'type' => new ChannelSiteFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var ChannelInterface $channel */
                $channel = $event->getData();
                $form = $event->getForm();

                /** @var ChannelSiteInterface[] $channelSites */
                $channelSites = $event->getForm()->get('channelSites')->getData();

                if($channelSites === null) {
                    $form->get('channelSites')->addError(new FormError('channelSites must be an array string'));
                    return;
                }

                foreach ($channelSites as $cs) {
                    if (!$cs->getChannel() instanceof ChannelInterface) {
                        $cs->setChannel($channel);
                    }
                }

                if ($channelSites instanceof Collection) {
                    $channelSites = $channelSites->toArray();
                }

                $channelSites = array_unique($channelSites);
                $channel->setChannelSites($channelSites);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Channel::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_channel';
    }
}