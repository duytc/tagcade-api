<?php

namespace Tagcade\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\IvtPixel;
use Tagcade\Form\DataTransformer\RoleToUserEntityTransformer;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\User\Role\AdminInterface;

class IvtPixelFormType extends AbstractRoleSpecificFormType
{

    static $IVT_PIXEL_SUPPORTED_MACRO = ['country', 'timestamp', 'device_id', 'device_name', 'demand_sell_price'];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(ObjectManager $om)
    {
        $this->em = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('urls')
            ->add('fireOn', ChoiceType::class, array(
                'choices' => array(
                    'request' => 'request',
                    'impression' => 'impression'
                ),
            ))
            ->add('runningLimit');

        $builder->add('ivtPixelWaterfallTags', 'collection', array(
                'mapped' => true,
                'type' => new IvtPixelWaterfallTagFormType(),
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

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
                /** @var IvtPixelInterface $ivtPixel */
                $ivtPixel = $event->getData();
                $form = $event->getForm();

                $ivtPixelName = $ivtPixel->getName();
                if (empty($ivtPixelName) || strlen($ivtPixelName) < 2) {
                    $event->getForm()->addError(new FormError('name should not be blank and must be more than two characters'));
                }

                $ivtPixel->setName($ivtPixelName);

                $ivtPixelUrls = $ivtPixel->getUrls();
                if (empty($ivtPixelUrls) || !is_array($ivtPixelUrls)) {
                    $event->getForm()->addError(new FormError('urls must be an array string and not null'));
                }

                if (!$this->validateMacroInUrls($ivtPixelUrls)) {
                    $event->getForm()->addError(new FormError('There is a macro which does not support in system'));
                }

                $ivtPixel->setUrls($ivtPixelUrls);

                $ivtPixelRunningLimit = $ivtPixel->getRunningLimit();
                if (empty($ivtPixelRunningLimit) || $ivtPixelRunningLimit < 0) {
                    $event->getForm()->addError(new FormError('runningLimit must not be less than 0'));
                }

                if (empty($ivtPixelRunningLimit) || $ivtPixelRunningLimit > 100) {
                    $event->getForm()->addError(new FormError('runningLimit must be equal or less than 100'));
                }

                $ivtPixel->setRunningLimit($ivtPixelRunningLimit);

                /**
                 *  Reset ivt pixel
                 */
                $ivtPixelWaterfallTags = $ivtPixel->getIvtPixelWaterfallTags();

                foreach ($ivtPixelWaterfallTags as $ivtPixelWaterfallTag) {
                    if (!$ivtPixelWaterfallTag instanceof IvtPixelWaterfallTagInterface) {
                        continue;
                    }

                    $ivtPixelWaterfallTag->setIvtPixel($ivtPixel);
                }

                $ivtPixel->setIvtPixelWaterfallTags($ivtPixelWaterfallTags);
            }
        );
    }

    /**
     * @param array $ivtPixelUrls
     * @return bool
     */
    private function validateMacroInUrls(array $ivtPixelUrls)
    {
        foreach ($ivtPixelUrls as $ivtPixelUrl) {
            $macros = $this->getMacrosInUrl($ivtPixelUrl);
            $unSupportMacros = array_diff($macros, self::$IVT_PIXEL_SUPPORTED_MACRO);
            if (!empty($unSupportMacros)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $url
     * @return array
     */
    private function getMacrosInUrl($url)
    {
        if (empty($url)) {
            return [];
        }

        $macros = [];
        $regex = '/\$\{(.*?)\}/';
        if (false !== preg_match_all($regex, $url, $matches)) {
            $macros = $matches[1];
        }

        return $macros;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => IvtPixel::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ivt_pixel';
    }
}