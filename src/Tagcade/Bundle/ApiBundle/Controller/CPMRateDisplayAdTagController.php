<?php

namespace Tagcade\Bundle\ApiBundle\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Controller\RestController;
use Tagcade\Handler\HandlerInterface;

/**
 * Allow publisher to set their ad tag rate then recalculate revenue
 * Class CPMRateDisplayAdTagController
 * @package Tagcade\Bundle\ReportApiBundle\Controller
 *
 * @Rest\RouteResource("CPMRateDisplayAdTag")
 */
class CPMRateDisplayAdTagController extends  RestController implements ClassResourceInterface
{
    /**
     * Create a cpm rate from the submitted data
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
     * Update an existing CPM rate from the submitted data or create a new CPM
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
     * @return string
     */
    protected function getResourceName()
    {
        // TODO: Implement getResourceName() method.
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        // TODO: Implement getGETRouteName() method.
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        // TODO: Implement getHandler() method.
    }

} 