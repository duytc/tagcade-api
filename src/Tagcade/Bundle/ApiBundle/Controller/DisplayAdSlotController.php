<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Bundle\ApiBundle\Behaviors\UpdateSiteForAdSlotValidator;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Handler\Handlers\Core\AdSlotHandlerAbstract;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform\AdSlotInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("DisplayAdslot")
 */
class DisplayAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    use UpdateSiteForAdSlotValidator;
    /**
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "displayadslot.summary", "site.summary", "librarydisplayadslot.summary", "user.summary", "slotlib.summary"}
     * )
     * Get all display ad slots
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     *      serializerGroups={"adslot.detail", "displayadslot.detail", "site.summary", "librarydisplayadslot.detail", "user.summary", "slotlib.summary"}
     * )
     * Get a single display adSlot for the given id
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     * Get tag in js for an ad slot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id
     * @return View
     */
    public function getJstagAction($id)
    {
        /** @var DisplayAdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->createJsTags($adSlot);
    }


    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * Create a display adSlot from the submitted data
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     * Update the position of all ad tags in an display ad slot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagsPositionsAction(Request $request, $id)
    {
        /** @var DisplayAdSlotInterface $adSlot */
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
     * Do cloning an ad slot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
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
            $this->get('tagcade_api.service.tag_library.ad_slot_cloner_service')->cloneAdSlot($originAdSlot, $newName, $site);
        }
        else {
            $this->get('tagcade_api.service.tag_library.ad_slot_cloner_service')->cloneAdSlot($originAdSlot, $newName);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Update an existing display adSlot from the submitted data or create a new display adSlot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     * Update an existing display adSlot from the submitted data or create a new display adSlot at a specific location
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
        /** @var BaseAdSlotInterface $adSlot */
        $adSlot = $this->one($id);
        $this->validateSiteWhenUpdatingAdSlot($request, $adSlot);

        if(array_key_exists('libraryAdSlot', $request->request->all()))
        {
            if(!is_array($request->request->get('libraryAdSlot'))) {
                $libraryAdSlotId = (int)$request->request->get('libraryAdSlot');
                /** @var DisplayAdSlotInterface $adSlot */
                $adSlot = $this->getOr404($id);

                $newLibraryAdSlot = $this->get('tagcade.domain_manager.library_ad_slot')->find($libraryAdSlotId);
                if(!$newLibraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
                    throw new InvalidArgumentException('LibraryAdSlot not existed');
                }

                $this->checkUserPermission($newLibraryAdSlot);

                if($adSlot->getLibraryAdSlot()->getId() !== $libraryAdSlotId && $newLibraryAdSlot->isVisible()) {
                    // create new ad tags
                    $this->get('tagcade_api.service.tag_library.replicator')->replicateFromLibrarySlotToSingleAdSlot($newLibraryAdSlot, $adSlot);
                }
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing display adSlot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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

    /**
     * Get Ad tags of an ad slot
     *
     * @Rest\View(
     *      serializerGroups={"adtag.summary", "site.summary", "user.summary", "libraryadtag.summary", "adnetwork.summary"}
     * )
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param $id
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdtagsAction($id)
    {
        /** @var DisplayAdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_tag')
            ->getAdTagsForAdSlot($adSlot);
    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $newAdTagOrderIds
     *
     * @return HandlerEventLog
     */
    private function createUpdatePositionEventLog(DisplayAdSlotInterface $adSlot, array $newAdTagOrderIds)
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