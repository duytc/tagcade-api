<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class AdTagFormType extends AbstractRoleSpecificFormType
{
    protected $adSlotRepository;

    const AD_TYPE_HTML = 0;
    const AD_TYPE_IMAGE = 1;

    public function __construct(AdSlotRepositoryInterface $adSlotRepository){
        $this->adSlotRepository = $adSlotRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // admins can add adSlots for any publisher
            $builder->add('adSlot');

        }
        else if ($this->userRole instanceof PublisherInterface) {

            $builder
                ->add('adSlot', 'entity', array(
                    'class' => AdSlotAbstract::class,
                    'choices' => $this->getReportableAdSlotsForPublisher($this->userRole)
                ));

        } else {
            throw new LogicException('A valid user role is required by AdTagFormType');
        }

        $builder
            ->add('position')
            ->add('frequencyCap')
            ->add('active')
            ->add('rotation')
            ->add('libraryAdTag', 'entity', array('class' => LibraryAdTag::class))
        ;

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