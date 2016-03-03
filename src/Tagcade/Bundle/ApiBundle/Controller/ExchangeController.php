<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;

/**
 * @Rest\RouteResource("Exchange")
 */
class ExchangeController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all exchange
     * @ApiDoc(
     *  section="Ad Tags",
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
     * Get a single exchange for the given id
     *
     * @ApiDoc(
     *  section="Ad Tags",
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
     * @Security("has_role('ROLE_ADMIN')")
     * Create a exchange from the submitted data
     * @ApiDoc(
     *  section="Ad Tags",
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
        $user = $this->getUser();

        if (!$user instanceof AdminInterface) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to %s this %s or it does not exist',
                    'edit',
                    $this->getResourceName()
                )
            );
        }

        return $this->post($request);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * Update an existing exchange from the submitted data or create a new exchange
     * @ApiDoc(
     *  section="Ad Tags",
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
        $user = $this->getUser();

        if (!$user instanceof AdminInterface) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to %s this %s or it does not exist',
                    'edit',
                    $this->getResourceName()
                )
            );
        }

        return $this->put($request, $id);
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * Update an existing exchange from the submitted data or create a new exchange at a specific location
     *
     * @ApiDoc(
     *  section="Ad Tags",
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
        $user = $this->getUser();

        if (!$user instanceof AdminInterface) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to %s this %s or it does not exist',
                    'edit',
                    $this->getResourceName()
                )
            );
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing exchange
     *
     * @ApiDoc(
     *  section="Exchange",
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
        $user = $this->getUser();

        if (!$user instanceof AdminInterface) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to %s this %s or it does not exist',
                    'delete',
                    $this->getResourceName()
                )
            );
        }

        return $this->delete($id);
    }

    protected function getResourceName()
    {
        return 'exchange';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_exchange';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.exchange');
    }
}
