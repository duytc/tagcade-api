<?php

namespace Tagcade\Handler\Handlers\Core;

use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Form\Type\RoleSpecificFormTypeInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

abstract class SubPublisherHandlerAbstract extends RoleHandlerAbstract
{
    /** @var SiteManagerInterface */
    protected $siteManager;

    public function __construct(FormFactoryInterface $formFactory, RoleSpecificFormTypeInterface $formType, $domainManager, SiteManagerInterface $siteManager, UserRoleInterface $userRole = null)
    {
        parent::__construct($formFactory, $formType, $domainManager, $userRole);

        $this->siteManager = $siteManager;
    }

    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return SubPublisherManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * custom post function to support add sites for subPublisher
     * @inheritdoc
     */
    public function post(array $parameters)
    {
        // 1. get list site ids (will belong to subPublisher), also remove from params
        $sites = array_key_exists('sites', $parameters) ? $parameters['sites'] : null;

        if (array_key_exists('sites', $parameters)) unset($parameters['sites']);

        // 2. normal create subPublisher
        /** @var SubPublisherInterface $subPublisher */
        $subPublisher = parent::post($parameters);

        // 3. set subPublisher for sites above
        if (is_array($sites)) {
            $this->setSubPublisherForSites($subPublisher, $sites);
        }

        return $subPublisher;
    }

    /**
     * custom patch function to support add sites for subPublisher
     * @inheritdoc
     */
    public function patch(ModelInterface $subPublisher, array $parameters)
    {
        if (!$subPublisher instanceof SubPublisherInterface) {
            return $subPublisher; // just return input
        }

        // 1. get list site ids (will belong to subPublisher), also remove from params
        $sites = array_key_exists('sites', $parameters) ? $parameters['sites'] : null;

        if (array_key_exists('sites', $parameters)) unset($parameters['sites']);

        // 2. normal patch subPublisher
        /** @var SubPublisherInterface|ModelInterface $subPublisher */
        $subPublisher = parent::patch($subPublisher, $parameters);

        // 3. set subPublisher for sites above
        if (is_array($sites)) {
            $this->setSubPublisherForSites($subPublisher, $sites);
        }

        // 4. unset subPublisher for sites that absent from sites param
        /** @var array|SiteInterface[] $oldSites */
        $oldSites = $this->siteManager->getSitesForPublisher($subPublisher);

        foreach ($oldSites as $site) {
            // keep if site existed in sites param
            if (in_array($site, $sites)) {
                continue;
            }

            $site->setSubPublisher(null);
            $this->siteManager->save($site);
        }

        return $subPublisher;
    }

    /**
     * set SubPublisher For Sites
     *
     * @param SubPublisherInterface $subPublisher
     * @param array|SiteInterface[] $sites
     */
    protected function setSubPublisherForSites(SubPublisherInterface $subPublisher, array $sites)
    {
        foreach ($sites as $site) {
            // skip if site already belongs to a SubPublisher!!
            if ($site->getSubPublisher() instanceof SubPublisherInterface) {
                continue;
            }

            $site->setSubPublisher($subPublisher);
            $this->siteManager->save($site);
        }
    }
}