<?php
namespace Tagcade\Bundle\AdminApiBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportSiteConfigManagerInterface;
use Tagcade\Handler\HandlerAbstract;
use Tagcade\Handler\HandlerInterface;

class SourceReportSiteConfigHandler extends HandlerAbstract implements HandlerInterface
{
    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, SourceReportSiteConfigManagerInterface $domainManager)
    {
        parent::__construct($formFactory, $formType, $domainManager, 'SourceReportSiteConfigHandler');
    }
}