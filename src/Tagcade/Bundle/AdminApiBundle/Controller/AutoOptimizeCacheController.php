<?php


namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheParam;
use Tagcade\Domain\DTO\Core\Video\AutoOptimizeVideoCacheParam;
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
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->view(['message' => "Bad Request: data json invalid!", 'code' => Codes::HTTP_BAD_REQUEST]);
        }

        if (!array_key_exists('platform_integration', $data)) {
            return $this->view(['message' => "Bad Request: data json invalid! Missing key platform_integration", 'code' => Codes::HTTP_BAD_REQUEST]);
        }
        $platformIntegration = $data['platform_integration'];

        switch ($platformIntegration) {
            case 'pubvantage':
                try {
                    $cacheParam = new AutoOptimizeCacheParam($data);
                } catch (InvalidArgumentException $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_BAD_REQUEST]);
                }

                $autoOptimizedCache = $this->get('tagcade.cache.v2.auto_optimized_cache');
                try {
                    $autoOptimizedCache->updateCacheForAdSlots($cacheParam);
                } catch (\Exception $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_INTERNAL_SERVER_ERROR]);
                }
                break;
            case 'pubvantage-video':
                try {
                    $cacheParam = new AutoOptimizeVideoCacheParam($data);
                } catch (InvalidArgumentException $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_BAD_REQUEST]);
                }

                $autoOptimizedCache = $this->get('tagcade.cache.video.auto_optimized_video_cache');
                try {
                    $autoOptimizedCache->updateCacheForWaterfallTags($cacheParam);
                } catch (\Exception $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_INTERNAL_SERVER_ERROR]);
                }
                break;
            default:
                return $this->view(['message' => "Bad Request: ...", 'code' => Codes::HTTP_BAD_REQUEST]);
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
    public function getPreviewPositionForPubvantageAction(Request $request)
    {
        $data = $request->request->all();
        $data = json_decode($data['data'], true);

        if (!array_key_exists('platform_integration', $data)) {
            return $this->view(['message' => "Bad Request: data json invalid! Missing key platform_integration", 'code' => Codes::HTTP_BAD_REQUEST]);
        }
        $platformIntegration = $data['platform_integration'];

        switch ($platformIntegration) {
            case 'pubvantage':
                try {
                    $cacheParam = new AutoOptimizeCacheParam($data);
                } catch (InvalidArgumentException $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_BAD_REQUEST]);
                }

                $autoOptimizedCache = $this->get('tagcade.cache.v2.auto_optimized_cache');
                try {
                    $previewPositionResult = $autoOptimizedCache->getPreviewPositionForAdSlots($cacheParam);
                } catch (\Exception $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_INTERNAL_SERVER_ERROR]);
                }

                break;

            case 'pubvantage-video':
                try {
                    $cacheParam = new AutoOptimizeVideoCacheParam($data);
                } catch (InvalidArgumentException $exception) {
                    return $this->view(['message' => $exception->getMessage(), 'code' => Codes::HTTP_BAD_REQUEST]);
                }

                $autoOptimizedVideoCache = $this->get('tagcade.cache.video.auto_optimized_video_cache');
                try {
                    $previewPositionResult = $autoOptimizedVideoCache->getPreviewPositionForWaterfallTags($cacheParam);
                } catch (\Exception $exception) {
                    return $this->view(['message' =>  $exception->getMessage(), 'code' => Codes::HTTP_INTERNAL_SERVER_ERROR]);
                }

                break;

            default:
                return $this->view(['message' => "Bad Request: ...", 'code' => Codes::HTTP_BAD_REQUEST]);
        }

        return $previewPositionResult;
    }
}