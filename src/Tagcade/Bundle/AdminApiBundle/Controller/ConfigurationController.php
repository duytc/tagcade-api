<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Tagcade\Bundle\AdminApiBundle\Utils\ModuleNameMapper;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\DisplayAdSlotInterface;
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
     * @return array format as:
     * {
     *      "1":{
     *          "modules":[
     *              "displayAds",
     *              "analytics",
     *              "unifiedReport",
     *              "subPublisher",
     *              "videoAnalytics",
     *              "realTimeBidding"
     *          ],
     *          "config":{
     *              "videoAnalytics":{
     *                  "players":[
     *                      "5min",
     *                      "ooyala"
     *                  ]
     *              },
     *              "realTimeBidding":{
     *                  "exchanges":[
     *                      "index-exchange",
     *                      "open-x"
     *                  ]
     *              }
     *          }
     *      },
     *      ...
     * }
     *
     */
    public function getSiteConfigsAction()
    {
        $siteManager = $this->get('tagcade.domain_manager.site');
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
        $adSlotManager = $this->get('tagcade.domain_manager.ad_slot');

        $publishers = $publisherManager->allPublishers();
        $siteConfigs = array();
        /** @var PublisherInterface $publisher */
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new RuntimeException('expect PublisherInterface interface instance');
            }

            $modules = $publisher->getEnabledModules();
            $sites = $siteManager->getSitesForPublisher($publisher);

            /**@var SiteInterface $site */
            foreach ($sites as $site) {
                if ($site instanceof SiteInterface) {
                    $moduleConfigs = [];
                    $siteConfigs[$site->getId()] = [];

                    if ($publisher->hasVideoModule()) {
                        $moduleConfigs[] = $this->mapModuleConfig(array('MODULE_VIDEO_ANALYTICS' => array('players' => $site->getPlayers())));
                    }

                    if ($publisher->hasHeaderBiddingModule()) {
                        $adSlots = $adSlotManager->getDisplayAdSlotsForSite($site);

                        $adSlotsCfg = array_map(function ($adSlot) {
                            if (!$adSlot instanceof DisplayAdSlotInterface) {
                                return null;
                            }

                            return [
                                'id' => $adSlot->getId(),
                                'width' => $adSlot->getWidth(),
                                'height' => $adSlot->getHeight()
                            ];
                        }, $adSlots);

                        if (count($adSlotsCfg) > 0) {
                            $moduleConfigs[] = $this->mapModuleConfig(array('MODULE_HEADER_BIDDING' => array('adSlots' => $adSlotsCfg, 'bidders' => $publisher->getBidders())));
                        }
                    }

                    // build data for key 'config'
                    $configs = [];
                    if (count($moduleConfigs) > 0) {
                        foreach ($moduleConfigs as $modConf) {
                            foreach ($modConf as $name => $value) {
                                $configs[$name] = $value;
                            }
                        }
                    }

                    // notice: 'config' key existed only if config data has at least one item
                    $siteConfigs[$site->getId()] = count($configs) > 0
                        ? array('modules' => $this->mapModuleName($modules), 'config' => $configs)
                        : array('modules' => $this->mapModuleName($modules));
                }
            }
        }

        $response = new Response(json_encode($siteConfigs));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

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
     * @Rest\Get("/configurations/billing")
     **/
    public function getDefaultBillingConfigsAction()
    {
        return array (
            'display' => $this->getParameter('tc.display.billing.thresholds'),
            'video' => $this->getParameter('tc.video.billing.thresholds'),
            'header-bidding' => $this->getParameter('tc.header_bid.billing.thresholds'),
            'in-banner' => $this->getParameter('tc.inbanner.billing.thresholds'),
        );
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
