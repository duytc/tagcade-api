<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SourceReportEmailConfigFormType extends AbstractType {
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
        ;
    }
} 