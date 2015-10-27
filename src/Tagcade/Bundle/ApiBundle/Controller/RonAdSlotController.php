<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\AdSlotHandlerAbstract;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Service\StringUtil;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("RonAdslot")
 */
class RonAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     *
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.summary", "librarydynamicadslot.summary", "librarydisplayadslot.summary", "user.summary", "slotlib.summary", "ronadslot.summary", "ronadslotsegment.summary", "segment.summary"}
     * )
     * Get all ron ad slots
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return DisplayAdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.summary", "librarydynamicadslot.detail", "librarydisplayadslot.summary", "user.summary", "slotlib.summary", "ronadslot.summary", "librarydynamicadslot.detail" , "site.summary", "expression.detail", "libraryexpression.detail", "ronadslotsegment.summary", "segment.summary"}
     * )
     * Get a single ron adSlot for the given id
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
     * @return DisplayAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * @param int $id
     * @return View
     */
    public function getJstagAction($id)
    {
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->getTagsForSingleRonAdSlot($ronAdSlot);
    }

    /**
     * @Rest\View(
     *      serializerGroups={"dynamicadslot.minimum", "nativeadslot.summary", "displayadslot.summary", "site.minimum"}
     * )
     *
     * @Rest\QueryParam(name="domain", description="domain for search")
     *
     * @param int $id
     * @return FormTypeInterface|View
     */
    public function getAdslotAction($id)
    {
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->one($id);

        $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();
        if (!$libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
            throw new LogicException('invalid RonAdSlot');
        }

        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $domain = $paramFetcher->get('domain');

        $urlParsed = parse_url(strtolower($domain));
        if (!$urlParsed) {
            throw new LogicException('invalid domain');
        }
        $domain = $urlParsed['path'] === null ? $urlParsed['host'] : $urlParsed['path'];

        $adSlot = $this->get('tagcade.repository.ad_slot')->getAdSlotForPublisherAndDomainAndLibraryAdSlot($libraryAdSlot->getPublisher(), $libraryAdSlot, $domain);
        if (!$adSlot instanceof BaseAdSlotInterface) {
            throw new NotFoundHttpException('the requested ad slot not existed or you do not have enough permission');
        }

        return $adSlot;
    }

    /**
     * @Rest\View(
     *      serializerGroups={"dynamicadslot.minimum", "nativeadslot.summary", "displayadslot.summary", "site.minimum", "user.summary", "slotlib.summary"}
     * )
     *
     * @param Request $request
     * @param $id
     * @return FormTypeInterface|View
     */
    public function postAdslotAction(Request $request, $id)
    {
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->one($id);

        if (!array_key_exists('domain', $request->request->all())) {
            throw new InvalidArgumentException('domain can not be empty');
        }

        $domain = $request->get('domain');

        $urlParsed = parse_url(strtolower($domain));
        if (!$urlParsed) {
            throw new LogicException('invalid domain');
        }
        $domain = $urlParsed['path'] === null ? $urlParsed['host'] : $urlParsed['path'];

        $adSlot =  $this->get('tagcade.domain_manager.ron_ad_slot')->createAdSlotFromRonAdSlotAndDomain($ronAdSlot, $domain);

        $routeOptions = array(
            '_format' => $request->get('_format')
        );

        return view::create(array(
            'id' => $adSlot->getId(),
            'site' => array(
                'id' => $adSlot->getSite()->getId(),
                'domain' => $adSlot->getSite()->getDomain()
            )
        ), Codes::HTTP_CREATED, $routeOptions);
    }

    /**
     * Create a ron adSlot from the submitted data
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
     * Update an existing ron adSlot from the submitted data or create a new one
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
     * Update an existing ron adSlot from the submitted data or create a new one at a specific location
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
     * Delete an existing ron adSlot
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
        return 'ronadslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_ronadslot';
    }

    /**
     * @return AdSlotHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ron_ad_slot');
    }
}