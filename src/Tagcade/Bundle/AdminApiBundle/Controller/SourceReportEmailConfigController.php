<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Tagcade\Bundle\AdminApiBundle\Entity\SourceReportSiteConfig;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
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
        return $this->getHandler()->all();
    }

    /**
     * get SourceReportEmailConfig by id
     *
     * @param $id
     * @return \Tagcade\Model\ModelInterface
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get source report config for publisher
     *
     * @Rest\Get("/sourcereportemailconfigs/accounts/{publisherId}", requirements={"publisherId" = "\d+"})
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
     * @return array
     */
    public function getCreatorconfigAction()
    {
        //call service SourceReportConfigService to build JSON
        $sourceReportConfig = $this->get('tagcade_admin_api.service.source_report_config');

        return $sourceReportConfig->getAllSourceReportConfig();
    }

    /**
     * Edit SourceReportEmailConfig by id
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
     * Create new source report config for publisher
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
     * Delete SourceReportConfig by id
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