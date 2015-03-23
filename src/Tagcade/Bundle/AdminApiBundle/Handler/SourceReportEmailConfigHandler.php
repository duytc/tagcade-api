<?php

namespace Tagcade\Bundle\AdminApiBundle\Handler;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportEmailConfigManagerInterface;
use Tagcade\Handler\HandlerAbstract;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\ModelInterface;

class SourceReportEmailConfigHandler extends HandlerAbstract implements HandlerInterface
{
    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, SourceReportEmailConfigManagerInterface $domainManager)
    {
        parent::__construct($formFactory, $formType, $domainManager);
    }
} 