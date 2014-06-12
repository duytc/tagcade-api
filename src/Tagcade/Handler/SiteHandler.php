<?php

namespace Tagcade\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Exception\BadMethodCallException;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\SiteInterface;
use Tagcade\Form\Type\SiteType;
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

    /**
     * @param ObjectManager $om
     * @param $entityClass
     * @param FormFactoryInterface $formFactory
     * @param PublisherInterface $publisher If this is empty, the handler can still be used to retrieve sites, however many methods rely on a publisher
     */
    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, PublisherInterface $publisher = null)
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
        if ($this->publisher) {
            return $this->repository->getSitesForPublisher($this->publisher, $limit, $offset);
        }

        throw new BadMethodCallException('publisher must be set to retrieve a list of sites');
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
     * @throws BadMethodCallException
     */
    private function processForm(SiteInterface $site, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new SiteType(), $site, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);

        if (null == $site->getPublisher()) {
            if (null == $this->publisher) {
                throw new BadMethodCallException('Publisher is not set');
            }

            $site->setPublisher($this->publisher);
        }

        if ($form->isValid()) {
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