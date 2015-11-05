<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportEmailConfigFormType extends AbstractType {

    /** @var PublisherManagerInterface */
    private $publisherManager;

    public function __construct(PublisherManagerInterface $publisherManager)
    {
        $this->publisherManager = $publisherManager;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'tagcade_form_admin_api_source_report_email_config';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('active')
            ->add('includedAll')
            ->add('includedAllSitesOfPublishers')
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT,
            function(FormEvent $event){
                $form = $event->getForm();

                /** @var SourceReportEmailConfigInterface $sourceReportEmailConfig */
                $sourceReportEmailConfig = $form->getData();
                $includedAllSitesOfPublishers = $sourceReportEmailConfig->getIncludedAllSitesOfPublishers();

                if (null === $includedAllSitesOfPublishers) {
                    // allow null
                    return;
                }

                if (!is_array($includedAllSitesOfPublishers)) {
                    // expect includedAllSitesOfPublishers as array of publisherIds
                    $form->get('includedAllSitesOfPublishers')->addError(new FormError('Expected list publishers, got ' . $includedAllSitesOfPublishers));
                    return;
                }

                // update field includedAllSitesOfPublishers
                foreach ($includedAllSitesOfPublishers as $publisherId) {
                    if (!$this->publisherManager->find($publisherId) instanceof PublisherInterface) {
                        $form->get('includedAllSitesOfPublishers')->addError(new FormError('Invalid publisher id #' . $publisherId));
                        return;
                    }
                }
            }
        );
    }
} 