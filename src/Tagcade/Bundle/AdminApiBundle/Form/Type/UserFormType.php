<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Form\Type\AbstractRoleSpecificFormType;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class UserFormType extends AbstractRoleSpecificFormType
{
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

//    private $userRole;

    private $oldSettings;

    public function __construct(UserEntityInterface $userRole)
    {
        $this->setUserRole($userRole);
//        $this->userRole = $userRole;
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

            // custom fields
            // even though in the system, roles and modules are simply just symfony2 roles
            // we separate the collection of them
//            ->add('userRoles', 'choice', [
//                'mapped' => false,
//                'empty_data' => null,
//                'multiple' => true,
//                'choices' => [
//                    'ROLE_PUBLISHER' => 'Publisher',
//                    'ROLE_ADMIN'     => 'Admin'
//                ],
//            ])
        ;

        if($this->userRole instanceof AdminInterface){
            $builder
                ->add('enabled')
                ->add('enabledModules', 'choice', [
                    'mapped' => false,
                    'empty_data' => null,
                    'multiple' => true,
                    'choices' => [
                        'MODULE_DISPLAY'         => 'Display',
                        'MODULE_VIDEO'           => 'Video',
                        'MODULE_ANALYTICS'       => 'Analytics',
                        'MODULE_FRAUD_DETECTION' => 'Fraud Detection'
                    ],
                ])
                ->add('billingRate')

                ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    /** @var User $user */
                    $user = $event->getData();
                    $form = $event->getForm();

//                $mainUserRole = $form->get('userRoles')->getData();
                    $modules = $form->get('enabledModules')->getData();

//                if (null !== $mainUserRole) {
//                    $user->setUserRoles((array) $mainUserRole);
//                }

                    if (null !== $modules && is_array($modules)) {
                        $user->setEnabledModules($modules);
                    }
                }
            );
        }

        //validate 'settings' field submitted by publisher
        //also merge all changes to current 'settings' of publisher (ui only submit with patched settings)
        if($this->userRole instanceof PublisherInterface) {
            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    /** @var PublisherInterface $publisher */
                    $publisher = $form->getData();
                    $this->oldSettings = $publisher->getSettings();
                });

            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function(FormEvent $event) {
                    $form = $event->getForm();
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
            );
        }
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