<?php

namespace Tagcade\Bundle\AdminApiBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SourceReportSiteConfigFormType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'tagcade_form_admin_api_source_report_site_config';
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            -> add('sites');
    }
}