<?php

namespace Tagcade\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Model\SiteInterface;
use Tagcade\Form\Type\SiteType;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\User\Role\PublisherInterface;

class SiteHandler implements SiteHandlerInterface
{
    private $om;
    private $entityClass;

    /**
     * @var \Tagcade\Repository\SiteRepository
     */
    private $repository;
    private $formFactory;

    /**
     * @var PublisherInterface|null
     */
    private $publisher;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, PublisherInterface $publisher)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->publisher = $publisher;
    }

    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = 0)
    {
        return $this->repository->getSitesForPublisher($this->publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function post(array $parameters)
    {
        $site = $this->createSite();

        return $this->processForm($site, $parameters, 'POST');
    }

    /**
     * @inheritdoc
     */
    public function put(SiteInterface $site, array $parameters)
    {
        return $this->processForm($site, $parameters, 'PUT');
    }

    /**
     * @inheritdoc
     */
    public function patch(SiteInterface $site, array $parameters)
    {
        return $this->processForm($site, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param SiteInterface $site
     * @param array $parameters
     * @param String $method
     *
     * @return SiteInterface
     *
     * @throws InvalidFormException
     */
    private function processForm(SiteInterface $site, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new SiteType(), $site, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            if (null == $site->getPublisher()) {
                $site->setPublisher($this->publisher);
            }

            $this->om->persist($site);
            $this->om->flush($site);

            return $site;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createSite()
    {
        return new $this->entityClass();
    }
}