<?php

namespace Tagcade\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Model\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class PublisherSiteHandler extends SiteHandler
{
    /**
     * @inheritdoc
     * @param PublisherInterface $publisher
     */
    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $formType, PublisherInterface $publisher)
    {
        parent::__construct($om, $entityClass, $formFactory, $formType, $publisher);
    }

    public function setPublisher(PublisherInterface $publisher)
    {
        $this->setUserRole($publisher);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getRepository()->getSitesForPublisher($this->getUserRole(), $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(SiteInterface $site, array $parameters, $method = "PUT")
    {
        if (null == $site->getPublisher()) {
            $site->setPublisher($this->getUserRole());
        }

        return parent::processForm($site, $parameters, $method);
    }
}