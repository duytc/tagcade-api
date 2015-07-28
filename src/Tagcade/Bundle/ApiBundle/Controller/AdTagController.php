<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;

/**
 * @Rest\RouteResource("Adtag")
 */
class AdTagController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad tags
     * @Rest\View(
     *      serializerGroups={"adtag.summary", "adslot.summary", "nativeadslot.summary", "site.summary", "user.summary", "adtaglibrary.ref", "adnetwork.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return AdTagInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * @Rest\View(
     *      serializerGroups={"adtag.detail", "adslot.summary", "nativeadslot.summary", "nativeadslotlib.summary", "displayadslotlib.summary", "site.summary", "user.summary", "adtaglibrary.ref", "adnetwork.summary"}
     * )
     * @Rest\View(serializerEnableMaxDepthChecks=false)
     *
     * Get a single adTag for the given id
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return AdTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a adTag from the submitted data
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postAction(Request $request)
    {
        return $this->post($request);
    }

    /**
     * Update an existing adTag from the submitted data or create a new adTag
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when the resource is created",
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function putAction(Request $request, $id)
    {
        return $this->put($request, $id);
    }

    /**
     * Update estCpm for ad tag.
     *
     * @ApiDoc(
     *  section = "adTags",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", description="Cpm rate of adTag")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return View
     */
    public function putEstcpmAction($id)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        //check param estCpm is number?
        $estCpmParam = $paramFetcher->get('estCpm');
        if(!is_numeric($estCpmParam)) {
            throw new InvalidArgumentException('estCpm should be numeric');
        }

        $estCpm = (float)$estCpmParam;
        if (!is_numeric($estCpm) || $estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be numeric and positive value');
        }

        $adTag = $this->get('tagcade.domain_manager.ad_tag')->find($id);
        if (!$adTag) {
            throw new NotFoundHttpException('That adTag does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adTag)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.worker.manager')->updateRevenueForAdTag($adTag, $estCpm, $startDate, $endDate);

        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
        $event = new HandlerEventLog('PUT', $adTag);
        $event->addChangedFields('estCpm', '', $estCpm, $startDate, $endDate);
        $this->getHandler()->dispatchEvent($event);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update an existing adTag from the submitted data or create a new adTag at a specific location
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request, $id)
    {
        return $this->patch($request, $id);
    }

    /**
     * Delete an existing adTag
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteAction($id)
    {
        return $this->delete($id);
    }

    protected function getResourceName()
    {
        return 'adtag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_adtag';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ad_tag');
    }
}
