<?php

namespace Tagcade\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class AdTagFormType extends AbstractRoleSpecificFormType
{
    protected $adSlotRepository;

    const AD_TYPE_HTML = 0;
    const AD_TYPE_IMAGE = 1;

    protected $autoIncreasePosition = false;

    public function __construct(AdSlotRepositoryInterface $adSlotRepository){
        $this->adSlotRepository = $adSlotRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('position')
            ->add('frequencyCap')
            ->add('active')
            ->add('rotation')
            ->add('impressionCap')
            ->add('networkOpportunityCap')
            ->add('passback')
            ->add('libraryAdTag', 'entity', array(
                    'class' => LibraryAdTag::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('libAdTag')->select('libAdTag');
                    }
                ))
            ->add('autoIncreasePosition', null, array('mapped' => false))
            ;

        // TODO: need re-check the var "AdSlotRepositoryInterface $er", may be incompatible type, must EntityRepository???
        $builder->add('adSlot', 'entity', array(
                'class' => AdSlotAbstract::class,
                'query_builder' => function (AdSlotRepositoryInterface $er) {
                    $qb = $this->userRole instanceof AdminInterface ? $er->createQueryBuilder('slot')->select('slot') : $er->getAdSlotsForPublisherQuery($this->userRole);
                    return $qb;
                }
            )
        );

        //$autoIncreasePosition = false;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $adTag = $event->getData();

                //create new Library
                if(array_key_exists('libraryAdTag', $adTag) && is_array($adTag['libraryAdTag'])){
                    $form->remove('libraryAdTag');
                    $form->add('libraryAdTag', new LibraryAdTagFormType($this->userRole));
                }

                if (array_key_exists('autoIncreasePosition', $adTag)){
                    $this->autoIncreasePosition = $adTag['autoIncreasePosition'];
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var AdTagInterface $adTag */
                $adTag = $event->getData();

                if ($this->autoIncreasePosition == true) {
                    // reset var $autoIncreasePosition
                    $this->autoIncreasePosition = false;

                    if ($adTag->getPosition() != null) {
                        // temporarily set AutoIncreasePosition field to ad tag for using when saving ad tag
                        $adTag->setAutoIncreasePosition(true);
                    }
                }
            }
        );
    }

    /**
     * get all adSlots (include display-adSlots and native-adSlots) for Publisher
     * @param PublisherInterface $publisher
     * @return array
     */
    protected function getReportableAdSlotsForPublisher(PublisherInterface $publisher) {

        return $this->adSlotRepository->getReportableAdSlotsForPublisher($publisher);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => AdTag::class,
                'cascade_validation' => true,
            ])
        ;
    }

    public function getName()
    {
        return 'tagcade_form_ad_tag';
    }
}