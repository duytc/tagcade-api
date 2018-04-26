<?php


namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheParam;
use Tagcade\Exception\InvalidArgumentException;
use FOS\RestBundle\View\View;

class AutoOptimizeCacheController extends FOSRestController
{
    /**
     *
     * @Rest\Post("/autooptimizecache")
     *
     * Update cache for adSlots base scores are submitted form UR
     *
     * @ApiDoc(
     *  section = "Auto Optimize Cache",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function updateAction(Request $request)
    {
        $data = $request->request->all();
        $data = json_decode($data['data'], true);

        try {
            $cacheParam = new AutoOptimizeCacheParam($data);
        } catch (InvalidArgumentException $exception) {
            return $this->view(['message' => "Bad Request", 'code' => Codes::HTTP_BAD_REQUEST]);
        }

        $autoOptimizedCache = $this->get('tagcade.cache.v2.auto_optimized_cache');

        try {
            $autoOptimizedCache->updateCacheForAdSlots($cacheParam);
        } catch (\Exception $exception) {
            return $this->view(['message' => "Error", 'code' => Codes::HTTP_INTERNAL_SERVER_ERROR]);
        }

        return $this->view(['message' => "Success", 'code' => Codes::HTTP_OK]);
    }

    /**
     *
     * @Rest\Post("/autooptimizecache/previewposition")
     *
     * preview new ad tags positions and compare to current positions
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function getPreviewPositionForAdSlotsAction(Request $request)
    {
        $data = $request->request->all();
        $data = json_decode($data['data'], true);

        try {
            $cacheParam = new AutoOptimizeCacheParam($data);
        } catch (InvalidArgumentException $exception) {
            return $this->view(['message' => "Bad Request", 'code' => Codes::HTTP_BAD_REQUEST]);
        }

        $autoOptimizedCache = $this->get('tagcade.cache.v2.auto_optimized_cache');

        try {
            $previewPositionResult = $autoOptimizedCache->getPreviewPositionForAdSlots($cacheParam);
        } catch (\Exception $exception) {
            return $this->view(['message' => "Error", 'code' => Codes::HTTP_INTERNAL_SERVER_ERROR]);
        }

        return $previewPositionResult;
    }
}