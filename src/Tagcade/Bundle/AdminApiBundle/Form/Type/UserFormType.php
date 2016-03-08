<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Form\Type\AbstractRoleSpecificFormType;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Service\StringUtilTrait;

class UserFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;
    static $REPORT_SETTINGS_ADTAG_KEY_VALUES = [
        'totalOpportunities',
        'firstOpportunities',
        'impressions',
        'verifiedImpressions',
        'unverifiedImpressions',
        'blankImpressions',
        'voidImpressions',
        'clicks',
        'passbacks',
        'fillRate',
    ];
    const MODULE_CONFIG = 'moduleConfigs';
    const VIDEO_MODULE = 'MODULE_VIDEO_ANALYTICS';
    const VIDEO_PLAYERS = 'players';
    protected $listPlayers = ['5min', 'defy', 'jwplayer5', 'jwplayer6', 'limelight', 'ooyala', 'scripps', 'ulive'];
    private $oldSettings;
    private $exchanges;

    public function __construct(UserEntityInterface $userRole, array $exchanges)
    {
        $this->setUserRole($userRole);
        $this->exchanges = array_map(function(array $exchange){
            return $exchange['abbreviation'];
        } , $exchanges) ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword')
            ->add('firstName')
            ->add('lastName')
            ->add('company')
            ->add('email')
            ->add('phone')
            ->add('city')
            ->add('state')
            ->add('address')
            ->add('postalCode')
            ->add('country')
            ->add('settings')
            ->add('exchanges')
        ;

        if($this->userRole instanceof AdminInterface){
            $builder
                ->add('tagDomain')
                ->add('enabled')
                ->add('enabledModules', 'choice', [
                    'mapped' => false,
                    'empty_data' => null,
                    'multiple' => true,
                    'choices' => [
                        'MODULE_DISPLAY'         => 'Display',
                        'MODULE_VIDEO_ANALYTICS' => 'Video',
                        'MODULE_ANALYTICS'       => 'Analytics',
                        'MODULE_FRAUD_DETECTION' => 'Fraud Detection',
                        'MODULE_UNIFIED_REPORT'  => 'Unified Report',
                        'MODULE_SUB_PUBLISHER'  => 'Sub Publisher',
                        'MODULE_RTB'  => 'RealTime Bidding'
                    ],
                ])
                ->add('billingRate');
        }

        //validate 'settings' field submitted by publisher
        //also merge all changes to current 'settings' of publisher (ui only submit with patched settings)
        if($this->userRole instanceof PublisherInterface) {
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    // validate exchanges before submitting if Publisher: only Admin has permission!!!
                    $form = $event->getForm();

                    if ($form->has('exchanges') && null !== $form->get('exchanges')->getData()) {
                        $form->get('exchanges')->addError(new FormError('you do not have enough permission to edit this entity'));
                        return;
                    }
                }
            );

            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    /** @var PublisherInterface $publisher */
                    $publisher = $form->getData();
                    $this->oldSettings = $publisher->getSettings();
                });
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                /** @var UserEntityInterface $publisher */
                $publisher = $event->getData();
                $form = $event->getForm();


                if ($this->userRole instanceof AdminInterface) {
                    $exchanges = $publisher->getExchanges();
                    foreach($exchanges as $exchange) {
                        if (!in_array($exchange, $this->exchanges)) {
                            $form->get('exchanges')->addError(new FormError(sprintf('exchange "%s" is currently not supported', $exchange)));
                        }

                        return;
                    }

                    if ($publisher->getId() === null) {
                        $publisher->generateAndAssignUuid();
                    }

                    $modules = $form->get('enabledModules')->getData();

                    if (null !== $modules && is_array($modules)) {
                        $publisher->setEnabledModules($modules);
                    }

                    //validate tag domain if there's
                    $tagDomain = $publisher->getTagDomain();

                    if (!is_array($tagDomain) && $tagDomain !== null) {
                        throw new InvalidArgumentException('expect array object');
                    }

                    if (is_array($tagDomain)) {
                        if (!isset($tagDomain['domain']) && isset($tagDomain['secure'])) {
                            throw new InvalidArgumentException('domain is missing');
                        }

                        if (isset($tagDomain['domain']) && !$this->validateDomain($tagDomain['domain'])) {
                            $form->get('tagDomain')->addError(new FormError(sprintf('"%s" is not a valid domain', $tagDomain['domain'])));
                            return;
                        }

                        if (isset($tagDomain['secure']) && !is_bool($tagDomain['secure'])) {
                            throw new InvalidArgumentException('expect true or false');
                        }
                    }
                }
                else if ($this->userRole instanceof PublisherInterface) {
                    /** @var PublisherInterface $publisher */
                    $publisher = $form->getData();
                    //this settings is only patched settings
                    $settings = $publisher->getSettings();

                    // 1. validate 'settings' field submitted by publisher
                    if (!isset($settings['view']['report']['performance']['adTag'])) {
                        $form->addError(new FormError("either 'view' or 'report' or 'performance' or 'adTag' field is missing!"));
                        return;
                    }

                    $adTagConfigs = $settings['view']['report']['performance']['adTag'];

                    foreach ($adTagConfigs as $adTagConfig) {
                        // keys 'key', 'label, 'show' are required
                        if (!isset($adTagConfig['key'])
                            || !isset($adTagConfig['label'])
                            || !isset($adTagConfig['show'])
                        ) {
                            $form->addError(new FormError("'key or label or show' field is missing!"));
                            break;
                        }

                        // all values of 'key' need to be supported
                        if (!in_array($adTagConfig['key'], self::$REPORT_SETTINGS_ADTAG_KEY_VALUES)) {
                            $form->addError(new FormError("key '" . $adTagConfig['key'] . "' is not supported yet!"));
                            break;
                        }

                        // value 'show' need to be boolean
                        if (!is_bool($adTagConfig['show'])) {
                            $form->addError(new FormError("value of show for '" . $adTagConfig['key'] . "' must be boolean!"));
                            break;
                        }
                    }


                    // 2. also merge all changes to current 'settings' of publisher (ui only submit with patched settings)
                    //    if not patch, old_settings (unchanged) will be removed
                    ////checking current settings: not existed or invalid => do nothing, using from ui
                    if($this->oldSettings !== null
                        && isset($this->oldSettings['view']['report']['performance']['adTag'])
                        && count($this->oldSettings['view']['report']['performance']['adTag']) > 0
                    ) {
                        $newSettings = array_map(function($settingItem) use ($settings) {
                            $settingItems = $settings['view']['report']['performance']['adTag'];
                            foreach($settingItems as $idx => $si) {
                                if($settingItem['key'] === $si['key']) {
                                    return $si;
                                }
                            }

                            return $settingItem;
                        }, $this->oldSettings['view']['report']['performance']['adTag']);
                        $this->oldSettings['view']['report']['performance']['adTag'] = $newSettings;
                        $publisher->setSettings($this->oldSettings);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'validation_groups' => ['Admin', 'Default'],
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_admin_api_user';
    }
}