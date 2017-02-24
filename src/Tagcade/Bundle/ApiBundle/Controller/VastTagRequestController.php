<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\SiteInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
/**
 * @Rest\RouteResource("VastTagRequest")
 */
class VastTagRequestController extends FOSRestController
{

    /**
     * Get vasttag history stored by Redis Server
     *
     * @Rest\QueryParam(name="uuid", nullable=false, description="number of item per page")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @return SiteInterface[]
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function cgetAction()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);

        $uuid = $params['uuid'];

        $page = filter_var($params['page'], FILTER_VALIDATE_INT) ? $params['page'] : 1;
        $limit = filter_var($params['limit'], FILTER_VALIDATE_INT) ? $params['limit'] : 10;

        $vastTagRequestManager =  $this->get('tagcade.cache.app.vast_tag_request_manager');
        return $vastTagRequestManager->getVastTagHistory($uuid, $page, $limit);
    }
}
