<?php
namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use FOS\RestBundle\Controller\Annotations as Rest;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * Class SourceReportSiteConfigController
 *
 * @Rest\RouteResource(resource="SourceReportSiteConfig")
 */
class SourceReportSiteConfigController extends RestControllerAbstract implements ClassResourceInterface
{
    const KEY_SITES = 'sites';

    /**
     * get SourceReportSiteConfig by siteConfigId
     *
     * @param $siteConfigId
     * @return \Tagcade\Model\ModelInterface
     */
    public function getAction($siteConfigId)
    {
        return $this -> one($siteConfigId);
    }

    /**
     * Get source report site configs for publisher and email
     *
     * @Rest\Get("/sourcereportsiteconfigs/accounts/{publisherId}/emailConfigs/{emailConfigId}", requirements={"publisherId" = "\d+", "emailConfigId" = "\d+"})
     *
     * @param int $publisherId
     *
     * @param int $emailConfigId
     *
     * @return SourceReportSiteConfigInterface[]
     *
     * @throws NotFoundHttpException if publisher|emailConfig not existed
     */
    public function getSourceReportSiteConfigForPublisherAndEmailConfigAction($publisherId, $emailConfigId)
    {
        $publisher = $this->getPublisher($publisherId);

        $sourceReportSiteConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_site_config');

        return $sourceReportSiteConfigManager->getSourceReportSiteConfigForPublisherAndEmailConfig($publisher, (int)$emailConfigId);
    }

    /**
     * Get source report site configs for emailConfig
     *
     * @Rest\Get("/sourcereportsiteconfigs/emailConfigs/{emailConfigId}", requirements={"emailConfigId" = "\d+"})
     *
     * @param int $emailConfigId
     *
     * @return SourceReportSiteConfigInterface[]
     *
     * @throws NotFoundHttpException if emailConfig not existed
     */
    public function getSourceReportSiteConfigForEmailConfigAction($emailConfigId)
    {
        $sourceReportEmailConfigManager = $this->get('tagcade_admin_api.domain_manager.source_report_email_config');

        $emailConfig = $sourceReportEmailConfigManager->find($emailConfigId);

        if (!$emailConfig instanceof SourceReportEmailConfigInterface) {
            return $this->view(null, Codes::HTTP_NOT_FOUND);

        }

        return $emailConfig->getSourceReportSiteConfigs();
    }

    /**
     * Edit a SourceReportSiteConfig for siteConfigId
     *
     * @param Request $request
     * @param int $siteConfigId
     * @return \FOS\RestBundle\View\View|\Symfony\Component\Form\FormTypeInterface
     */
    public function putAction(Request $request, $siteConfigId)
    {
        return $this->put($request, $siteConfigId);
    }

    /**
     * Add Sites for emailConfigId with sites[id1, id2, ...]
     *
     * @Rest\Post("sourcereportsiteconfigs/emailConfigs/{emailConfigId}", requirements={"emailConfigId" = "\d+"})
     *
     * @param Request $request
     * @param int $emailConfigId
     * @return \FOS\RestBundle\View\View|\Symfony\Component\Form\FormTypeInterface
     */
    public function addSitesForEmailAction(Request $request, $emailConfigId)
    {
        //get all sites from all params
        $params = $request->request->all();
        $defaultParams = array_fill_keys([
            self::KEY_SITES,
        ], null);
        $params = array_merge($defaultParams, $params);
        //all sites only site_ids
        $sites = $params[self::KEY_SITES];

        try {
            //get emailConfig by emailConfigId
            $sourceReportEmailConfigManager = $this -> get('tagcade_admin_api.domain_manager.source_report_email_config');
            $emailConfig = $sourceReportEmailConfigManager -> find($emailConfigId);
            // return if email not yet existed
            if (!$emailConfig instanceof SourceReportEmailConfigInterface) {
                return $this->view(null, Codes::HTTP_NOT_FOUND);
            }

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
            $sourceReportSiteConfigManager = $this -> get('tagcade_admin_api.domain_manager.source_report_site_config');
            $sourceReportSiteConfigManager -> saveSourceReportConfig($emailConfig, $availableSites);
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
     * Delete a SourceReportSiteConfig
     *
     * @param $siteConfigId
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction($siteConfigId)
    {
       return $this->delete($siteConfigId);
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
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'sourceReportSiteConfig';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'admin_api_1_get_sourcereportsiteconfig';
    }

    /**
     * @inheritdoc
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_admin_api.handler.source_report_site_config');
    }
}