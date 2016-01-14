<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Event\UpdateSourceReportEmailConfigEventLog;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * Class SourceReportEmailConfigController
 *
 * @Rest\RouteResource("SourceReportEmailConfig")
 */
class SourceReportEmailConfigController extends RestControllerAbstract implements ClassResourceInterface
{
    const KEY_EMAILS = 'emails';
    const KEY_SITES = 'sites';
    const KEY_INCLUDED_ALL = 'includedAll';
    const KEY_PUBLISHERS = 'includedAllSitesOfPublishers';

    /**
     * Get all source report configurations
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User[]
     */
    public function cgetAction()
    {
        /** @var SourceReportEmailConfigInterface[] $emailConfigs */
        $emailConfigs = $this->getHandler()->all();

        return array_map(function (SourceReportEmailConfigInterface $emailConfig) {
            $includedAllSitesOfPublishers = $emailConfig->getIncludedAllSitesOfPublishers();

            if (is_array($includedAllSitesOfPublishers) && sizeof($includedAllSitesOfPublishers) > 0) {
                $emailConfig->setIncludedAllSitesOfPublishers($this->mapDetailIncludedAllSites($includedAllSitesOfPublishers));
            }

            return $emailConfig;
        }, $emailConfigs);
    }

    /**
     * get SourceReportEmailConfig by id
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $id
     * @return \Tagcade\Model\ModelInterface
     */
    public function getAction($id)
    {
        /** @var SourceReportEmailConfigInterface $emailConfig */
        $emailConfig = $this->one($id);

        $includedAllSitesOfPublishers = $emailConfig->getIncludedAllSitesOfPublishers();

        if (is_array($includedAllSitesOfPublishers) && sizeof($includedAllSitesOfPublishers) > 0) {
            $emailConfig->setIncludedAllSitesOfPublishers($this->mapDetailIncludedAllSites($includedAllSitesOfPublishers));
        }

        return $emailConfig;
    }

    /**
     * Get source report config for publisher
     *
     * @Rest\Get("/sourcereportemailconfigs/accounts/{publisherId}", requirements={"publisherId" = "\d+"})
     *
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     *
     * @return SourceReportEmailConfigInterface[]
     *
     * @throws NotFoundHttpException if publisher not existed
     */
    public function getSourceReportConfigForPublisherAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        $sourceReportEmailConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_email_config');

        return $sourceReportEmailConfigManager->getSourceReportConfigForPublisher($publisher);
    }

    /**
     * Get JSON for all source report config
     *
     * @Rest\Get("/sourcereportemailconfig/creatorconfig")
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return array
     */
    public function getCreatorConfigAction()
    {
        //call service SourceReportConfigService to build JSON
        $sourceReportConfig = $this->get('tagcade_admin_api.service.source_report_config');

        return $sourceReportConfig->getAllSourceReportConfig();
    }

    /**
     * Edit SourceReportEmailConfig by id
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return View|FormTypeInterface
     */
    public function putAction(Request $request, $id)
    {
        return $this->put($request, $id);
    }

    /**
     * Include all reports to the email
     *
     * @Rest\Post("/sourcereportemailconfigs/emailIncludedAll")
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return View|FormTypeInterface
     */
    public function postSourceReportConfigIncludeAllAction(Request $request)
    {
        //get all emails and sites from all params
        $params = $request->request->all();
        $defaultParams = array_fill_keys([
            self::KEY_EMAILS
        ], null);
        $params = array_merge($defaultParams, $params);
        //all emails only email_values
        $emails = $params[self::KEY_EMAILS];

        try {
            $sourceReportEmailConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_email_config');
            $sourceReportEmailConfigManager->saveSourceReportConfigIncludedAll($emails);

            // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
            $event = new UpdateSourceReportEmailConfigEventLog('PUT');
            $event->addChangedFields('includedAll', 'false', 'true');
            $this->getHandler()->dispatchEvent($event);
        }
        catch (InvalidArgumentException $e) {
            return $this->view(null, Codes::HTTP_BAD_REQUEST);
        }
        catch(\Exception $e) {
            return $this->view(null, Codes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Include all reports of sites to the email where sites belong to special publishers
     *
     * @Rest\Post("/sourcereportemailconfigs/emailIncludedAllSites")
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return View|FormTypeInterface
     */
    public function postSourceReportConfigIncludeAllSitesAction(Request $request)
    {
        //get all emails and publishers from all params
        $params = $request->request->all();
        $defaultParams = array_fill_keys([
            self::KEY_EMAILS,
            self::KEY_PUBLISHERS
        ], null);
        $params = array_merge($defaultParams, $params);

        //all emails only email_values
        $emails = $params[self::KEY_EMAILS];

        //all publishers only publisher_ids
        $publishers = $params[self::KEY_PUBLISHERS];

        if (!is_array($emails)) {
            return $this->view('Expected emails as array', Codes::HTTP_BAD_REQUEST);
        }

        if (!is_array($publishers)) {
            return $this->view('Expected includedAllSitesOfPublishers as array', Codes::HTTP_BAD_REQUEST);
        }

        //filter all valid publishers
        $publisherManager = $this->get('tagcade_user_system_publisher.user_manager');
        $publishers = array_filter($publishers, function ($publisherId) use ($publisherManager) {
            if (!is_integer($publisherId)) {
                return false;
            }

            $publisher = $publisherManager->findUserBy(['id' => $publisherId]);
            return $publisher instanceof PublisherInterface;
        });
        //re-index publishers
        $publishers = array_values($publishers);

        try {
            $sourceReportEmailConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_email_config');
            $sourceReportEmailConfigManager->saveSourceReportConfigIncludedAllSites($emails, $publishers);

            // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
            $event = new UpdateSourceReportEmailConfigEventLog('PUT');
            $event->addChangedFields('includedAll', 'false', 'true');
            $this->getHandler()->dispatchEvent($event);
        } catch (InvalidArgumentException $e) {
            return $this->view($e->getMessage(), Codes::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->view(null, Codes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Create new source report config for publisher
     *
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     *
     * @return View
     *
     * @throws NotFoundHttpException if publisher not existed
     */
    public function postAction(Request $request)
    {
        //get all emails and sites from all params
        $params = $request->request->all();
        $defaultParams = array_fill_keys([
                self::KEY_EMAILS,
                self::KEY_SITES
            ], null);
        $params = array_merge($defaultParams, $params);
        //all emails only email_values
        $emails = $params[self::KEY_EMAILS];
        //all sites only site_ids
        $sites = $params[self::KEY_SITES];

        try {

            //get all sites by site_ids
            $siteManager = $this->get('tagcade.domain_manager.site');
            $sites = array_map(
                function($siteId) use ($siteManager){
                    return $siteManager->find($siteId);
                },
                $sites
            );

            $availableSites = array_filter($sites, function ($site) { return $site instanceof SiteInterface;});

            //calling repository to persis new SourceReportEmailConfig
            $sourceReportEmailConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_email_config');

            $sourceReportEmailConfigManager->saveSourceReportConfig($emails, $availableSites);

            // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
            $event = new UpdateSourceReportEmailConfigEventLog('POST');
            $event->addChangedFields('emails', '', ('[' . implode(', ', $emails) . ']'));
            foreach($availableSites as $site_i){
                /** @var SiteInterface $site_i */
                $event->addAffectedEntity('Site', '', $site_i->getDomain());
            }
            $this->getHandler()->dispatchEvent($event);
        }
        catch(InvalidArgumentException $invalidArgs) {
            return $this->view(null, Codes::HTTP_BAD_REQUEST);
        }
        catch(\Exception $e) {
            return $this->view($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Clone all site configs of existed email for other emails
     *
     * @Rest\Post("/sourcereportemailconfigs/{id}/clone", requirements={"id" = "\d+"})
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id
     * @param Request $request
     * @return View
     * @throws NotFoundHttpException if publisher not existed
     */
    public function cloneAction($id, Request $request)
    {
        //get all emails and all params
        $params = $request->request->all();
        $defaultParams = array_fill_keys([
            self::KEY_EMAILS
        ], null);
        $params = array_merge($defaultParams, $params);
        //all emails only email_values
        $emails = $params[self::KEY_EMAILS];

        if (!is_array($emails)) {
            return $this->view('Expected emails as array', Codes::HTTP_BAD_REQUEST);
        }

        /** @var SourceReportEmailConfigInterface $originalEmailConfig */
        $originalEmailConfig = $this->one($id);

        //calling domain manager to clone SourceReportEmailConfig
        $sourceReportEmailConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_email_config');

        $sourceReportEmailConfigManager->cloneSourceReportConfig($originalEmailConfig, $emails);

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Delete SourceReportConfig by id
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful"
     *  }
     * )
     *
     * @param $id
     * @return View
     */
    public function deleteAction($id)
    {
        return $this->delete($id);
    }

    /**
     * @param $publisherId
     * @return PublisherInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getPublisher($publisherId)
    {
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');

        $publisher = $publisherManager->findPublisher((int)$publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        return $publisher;
    }

    /**
     * map Detail IncludedAllSites, as [{'id' => <id>, 'company' => <company>}, {'id'..., 'company'...}, ...]
     *
     * @param array $includedAllSitesOfPublishers
     * @return array
     */
    private function mapDetailIncludedAllSites (array $includedAllSitesOfPublishers)
    {
        if (null === $includedAllSitesOfPublishers) {
            return [];
        }

        $detailIncludedAllSites = [];

        $publisherManager = $this->get('tagcade_user_system_publisher.user_manager');

        // map details for all publishers in IncludedAllSitesOfPublishers
        array_walk($includedAllSitesOfPublishers,
            function ($publisherId) use ($publisherManager, &$detailIncludedAllSites) {
                $publisher = $publisherManager->findUserBy(['id' => $publisherId]);
                if ($publisher instanceof PublisherInterface) {
                    $detailIncludedAllSites[] = [
                        'id' => $publisher->getId(),
                        'company' => $publisher->getCompany(),
                    ];
                }
            }
        );

        return $detailIncludedAllSites;
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'sourceReportEmailConfig';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'admin_api_1_get_sourcereportemailconfig';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_admin_api.handler.source_report_email_config');
    }
}