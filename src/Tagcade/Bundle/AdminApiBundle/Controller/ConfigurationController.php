<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tagcade\Bundle\AdminApiBundle\Utils\ModuleNameMapper;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class ConfigurationController extends RestControllerAbstract implements ClassResourceInterface
{
    use ModuleNameMapper;

    /**
     * return list site configurations with site name and enabled modules of the
     * publisher which posses the given site
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\Get("/configurations/sites")
     * @return array
     */
    public function getSiteConfigsAction()
    {
        $siteManager = $this->get('tagcade.domain_manager.site');
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');

        $publishers = $publisherManager->allPublishers();
        $siteConfigs = array();
        /** @var PublisherInterface $publisher */
        foreach($publishers as $publisher) {
            if(!$publisher instanceof PublisherInterface) {
               throw new RuntimeException('expect PublisherInterface interface instance');
            }

            $modules = $publisher->getEnabledModules();
//            $configs = $publisher->getConfig("MODULE_VIDEO");
            $sites = $siteManager->getSitesForPublisher($publisher);

            /**@var SiteInterface $site */
            foreach($sites as $site){
                if($site instanceof SiteInterface) {
                    if($publisher->hasVideoModule()){
                        $moduleConfigs = array('MODULE_VIDEO_ANALYTICS' => array('players' => $site->getPlayers()));
                        $siteConfigs[$site->getId()] = array('modules' => $this->mapModuleName($modules), 'config' => $this->mapModuleConfig($moduleConfigs));

                        continue;
                    }

                    if($publisher->hasRtbModule()){
                        $moduleConfigs = array('MODULE_RTB' => array('exchanges' => $publisher->getExchanges()));
                        $siteConfigs[$site->getId()] = array('modules' => $this->mapModuleName($modules), 'config' => $this->mapModuleConfig($moduleConfigs));

                        continue;
                    }

                    $siteConfigs[$site->getId()] = array('modules' => $this->mapModuleName($modules));
                }
            }
        }

        $response = new Response(json_encode($siteConfigs));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'admin_api_configuration_site_config';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
       throw new RuntimeException('Not support handler for configuration controller');
    }
}
