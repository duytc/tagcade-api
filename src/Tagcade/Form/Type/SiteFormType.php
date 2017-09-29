<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    /** @var  integer */
    protected $maxSubDomain;

    /**
     * SiteFormType constructor.
     * @param $maxSubDomain
     */
    public function __construct($maxSubDomain)
    {
        $this->maxSubDomain = $maxSubDomain;
    }


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
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                // validate players before submitting if Publisher and Publisher does not have Video Module
                if ($this->userRole instanceof PublisherInterface && !$this->userRole->getUser()->hasVideoAnalyticsModule()) {
                    $form = $event->getForm();

                    if ($form->has('players') && null !== $form->get('players')->getData()) {
                        $form->get('players')->addError(new FormError('this user does not have module video enabled'));
                        return;
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /* modify enableSourceReport for site */
                /** @var SiteInterface $site */
                $site = $event->getData();
                $form = $event->getForm();

                //validate domain
                $domain = $site->getDomain();
                /* check validate if $domain has value */
                /* and if $domain == null -> continue */
                if (!empty($domain)) {
                    if ($this->validateDomain($domain, $this->maxSubDomain) === FALSE) {
                        $form->get('domain')->addError(new FormError(sprintf("'%s' is not a valid domain", $domain)));
                    }
                }

                $site->setAutoCreate(false);

                if (!$site->getPublisher()->hasAnalyticsModule()) {
                    $site->setEnableSourceReport(false);
                }

                /** @var Collection|ChannelSiteInterface[] $channelSites */
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

                // validate players after submitting if Publisher and Publisher has Video Module
                $players = $form->get('players')->getData();
                if ($this->userRole instanceof PublisherInterface && $this->userRole->getUser()->hasVideoAnalyticsModule()) {
                    if (!is_array($players)) {
                        $form->get('players')->addError(new FormError('expect player config to be an array object'));
                        return;
                    } else {
                        foreach ($players as $player) {
                            if (!in_array($player, $this->listPlayers)) {
                                $form->get('players')->addError(new FormError(sprintf('players %s is not supported', $player)));
                                return;
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
                'allow_extra_fields' => true,
                'data_class' => Site::class,
                'cascade_validation' => true
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_site';
    }
}