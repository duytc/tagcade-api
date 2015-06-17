<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Handler\Handlers\Core\AdSlotHandlerAbstract;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\SiteInterface;

/**
 * @Rest\RouteResource("DisplayAdslot")
 */
class DisplayAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad slots
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return AdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single adSlot for the given id
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
     * @return AdSlotInterface
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
        /** @var AdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->createJsTags($adSlot);
    }
//
//    /**
//     * @Rest\Get("/variableDescriptor/{id}", requirements={"id" = "\d+"})
//     * @param Request $request
//     * @param $id
//     * @return View
//     */
//    public function getVariableDescriptorAction(Request $request, $id)
//    {
//        /** @var AdSlotInterface $adSlot */
//        $adSlot = $this->one($id);
//
//        return $this->getHandler()->getAdSlotVariableDescriptor($adSlot);
//    }

//    /**
//     * @Rest\Get("/configExpression/{id}", requirements={"id" = "\d+"})
//     * @param Request $request
//     * @param $id
//     * @return View
//     */
//    public function getConfigExpressionAction(Request $request, $id)
//    {
//        /** @var AdSlotInterface $adSlot */
//        $adSlot = $this->one($id);
//
//        return $this->getHandler()->getAdSlotConfigExpression($adSlot);
//    }

    /**
     * Create a adSlot from the submitted data
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
     * Update the position of all ad tags in an ad slot
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagsPositionsAction(Request $request, $id)
    {
        /** @var AdSlotInterface $adSlot */
        $adSlot = $this->one($id);
        $newAdTagOrderIds = $request->request->get('ids');

        if (!$newAdTagOrderIds) {
            throw new BadRequestHttpException("Ad tagIds parameter is required");
        }

        $result = array_values(
            $this->get('tagcade_app.service.core.ad_tag.ad_tag_position_editor')
                ->setAdTagPositionForAdSlot($adSlot, $newAdTagOrderIds)
        );

        $event = $this->createUpdatePositionEventLog($adSlot, $newAdTagOrderIds);
        $this->getHandler()->dispatchEvent($event);

        return $result;
    }

    /**
     * Update the position of all ad tags in an ad slot
     *
     * @Rest\POST("/displayadslots/{id}/clone", requirements={"id" = "\d+"})
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postCloneAction(Request $request, $id)
    {
        /** @var AdSlotInterface $originAdSlot */
        $originAdSlot = $this->one($id);
        $newName = $request->request->get('name');

        if(null === $newName || empty($newName) || !is_string($newName)){
            return $this->view(null, Codes::HTTP_BAD_REQUEST);
        }

        $siteId = $request->request->get('site');
        $site = null != $siteId ? $this->get('tagcade.domain_manager.site')->find($siteId) : null;

        if($site instanceof SiteInterface) {
            $this->checkUserPermission($site, 'edit');
            $this->getHandler()->cloneAdSlot($originAdSlot, $newName, $site);
        }
        else {
            $this->getHandler()->cloneAdSlot($originAdSlot, $newName);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Update an existing adSlot from the submitted data or create a new adSlot
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
     * Update an existing adSlot from the submitted data or create a new adSlot at a specific location
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
     * Delete an existing adSlot
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
        /**
         * @var AdSlotInterface $entity
         */
        $entity = $this->getOr404($id);
        $this->checkUserPermission($entity, 'edit');

        // dynamic ad slots that its expressions refer to this ad slot

        $expressions = $this->get('tagcade.repository.expression')->findBy(array('expectAdSlot' => $entity));
        $referencingDynamicAdSlots = array_map(
            function(ExpressionInterface $expression) {
                return $expression->getDynamicAdSlot();
            },
            $expressions
        );

        // dynamic ad slots that have default ad slot is this one.
        $referencingDynamicAdSlots = array_merge($referencingDynamicAdSlots, $entity->defaultDynamicAdSlots()->toArray());
        $referencingDynamicAdSlots = array_unique($referencingDynamicAdSlots);

        if (count($referencingDynamicAdSlots) > 0) {
            $view = $this->view(null, Codes::HTTP_BAD_REQUEST);
        }
        else {
            $this->getHandler()->delete($entity);
            $view = $this->view(null, Codes::HTTP_NO_CONTENT);
        }



        return $this->handleView($view);
    }

    public function getAdtagsAction($id)
    {
        /** @var AdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_tag')
            ->getAdTagsForAdSlot($adSlot);
    }

    /**
     * @param AdSlotInterface $adSlot
     * @param array $newAdTagOrderIds
     *
     * @return HandlerEventLog
     */
    private function createUpdatePositionEventLog(AdSlotInterface $adSlot, array $newAdTagOrderIds)
    {
        $newAdTagFlattenList = [];
        array_walk_recursive($newAdTagOrderIds, function ($adTagId) use (&$newAdTagFlattenList) {
            $newAdTagFlattenList[] = $adTagId;
        });

        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
        $event = new HandlerEventLog('POST', $adSlot);
        // backup for old adTags
        /** @var AdTagInterface[] $oldAdTags */
        $oldAdTags = $adSlot->getAdTags()->toArray(); // this is sorted already according to doctrine yml setting

        //// calculate old and new AdTagOrderNames for add changedFields
        /** @var AdTagInterface[] $adTags */
        $adTagsMap = [];
        foreach ($oldAdTags as $oldAdTag) {
            $adTagsMap[$oldAdTag->getId()] = $oldAdTag->getName();
        }

        $oldAdTagOrderNames = array_map(
            function (AdTagInterface $adTag) {
                return $adTag->getName();
            },
            $oldAdTags
        );

        $newAdTagOrderNames = array_map(
            function ($adTagId) use (&$adTagsMap) {
                return $adTagsMap[$adTagId];
            },
            $newAdTagFlattenList
        );

        $event->addChangedFields('position', implode(', ', $oldAdTagOrderNames), implode(', ', $newAdTagOrderNames));

        //// add affectedEntities
        /** @var AdTagInterface[] $adTags */
        $adTags = $adSlot->getAdTags();
        foreach ($adTags as $adTag) {
            $event->addAffectedEntity('AdTag', $adTag->getId(), $adTag->getName());
        }

        return $event;
    }

    protected function getResourceName()
    {
        return 'displayadslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_displayadslot';
    }

    /**
     * @return AdSlotHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ad_slot');
    }

    /**
     * compare AdTag By Position
     *
     * @param AdTagInterface $adTag_1
     * @param AdTagInterface $adTag_2
     * @return int -1 if  0 1
     */
    protected function compareAdTagByPosition(AdTagInterface $adTag_1, AdTagInterface $adTag_2)
    {
        return $adTag_1->getPosition() < $adTag_2->getPosition() ? -1 : $adTag_1->getPosition() > $adTag_2->getPosition() ? 1 : 0;
    }
}