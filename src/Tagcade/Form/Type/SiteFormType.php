<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\Site;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\ChannelSiteInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\StringUtilTrait;

class SiteFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;

    protected $listPlayers = ['5min', 'defy', 'jwplayer5', 'jwplayer6', 'limelight', 'ooyala', 'scripps', 'ulive'];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('domain')
            ->add('enableSourceReport')
            ->add('players');

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
                /* modify enableSourceReport for site */
                /** @var SiteInterface $site */
                $site = $event->getData();
                $form = $event->getForm();

                //validate domain
                $domain =  $site->getDomain();
                if ($this->validateDomain($domain) === FALSE) {
                    $form->get('domain')->addError(new FormError(sprintf("'%s' is not a valid domain", $domain)));
                }

                $site->setAutoCreate(false);

                if (!$site->getPublisher()->hasAnalyticsModule()) {
                    $site->setEnableSourceReport(false);
                }

                /** @var ChannelSiteInterface[] $channelSites */
                $channelSites = $event->getForm()->get('channelSites')->getData();

                if ($channelSites === null) {
                    $form->get('channelSites')->addError(new FormError('channelSites must be an array string'));
                    return;
                }

                foreach ($channelSites as $cs) {
                    if (!$cs->getSite() instanceof SiteInterface) {
                        $cs->setSite($site);
                    }
                }

                if ($channelSites instanceof Collection) {
                    $channelSites = $channelSites->toArray();
                }

                $channelSites = array_unique($channelSites);
                $site->setChannelSites($channelSites);

                $players = $form->get('players')->getData();
                if ($this->userRole instanceof PublisherInterface) {
                    if (!$this->userRole->getUser()->hasVideoModule()) {
                        if(is_array($players)) {
                            $form->get('players')->addError(new FormError('this user does not have module video enabled'));
                            return;
                        }
                    }
                    else {
                        if (!is_array($players)) {
                            $form->get('players')->addError(new FormError('expect player config to be an array object'));
                            return;
                        }
                        else {
                            foreach($players as $player){
                                if (!in_array($player, $this->listPlayers)) {
                                    $form->get('players')->addError(new FormError(sprintf('players %s is not supported', $player)));
                                    return;
                                }

                            }
                        }
                    }
                }

            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Site::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_site';
    }
}