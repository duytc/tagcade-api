<?php


namespace Tagcade\Bundle\ReportApiBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\Parameter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportCollection;
use FOS\RestBundle\View\View;

class VideoReportController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\QueryParam(name="filters", nullable=false)
     * @Rest\QueryParam(name="breakdowns", nullable=false)
     * @Rest\QueryParam(name="metrics", nullable=false)
     *
     * @Rest\View(
     *      serializerGroups={
     *              "demand_partner_demand_ad_tag.detail", "demand_partner_demand_partner.detail", "platform_account.detail",
     *              "platform_demand_ad_tag.detail", "platform_waterfall_tag.detail", "platform_platform.detail", "abstract_grouper.detail",
     *              "waterfall_tag_grouper.detail", "waterfall_tag_report_group.detail", "report_group.detail", "videoWaterfallTag.report", "videoWaterfallTagItem.detail",
     *              "videoDemandAdTag.videoReport", "libraryVideoDemandAdTag.report","videoDemandPartner.report", "user.summary", "video_report_type_account.detail", "video_report_type_waterfall_tag.detail",
     *              "video_report_type_demand_ad_tag.detail", "video_report_type_platform.detail", "video_report_type_demand_partner.detail",
     *              "video_report_type_demand_partner_demand_ad_tag.detail", "video_report_type_demand_partner_waterfall_tag.detail",
     *              "demand_partner_demand_partner_waterfall_tag.detail", "platform_publisher_report.detail", "video_report_type_video_publisher_demand_partner.detail",
     *              "platform_publisher.detail", "video_report_type_platform_publisher.detail", "videoPublisher.report", "video_report_video_publisher_demand_partner.detail",
     *          }
     * )
     * @ApiDoc(
     *  section = "Video report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  parameters = {
     *      {"name"="filters", "dataType"="string", "required"=true, "description"="filter values for video report"},
     *      {"name"="breakdowns", "dataType"="string", "required"=true, "description"="break down value for video report"},
     *      {"name"="metric", "dataType"="string", "required"=true, "description"=" all column to show in video report"}
     *  }
     * )
     *
     * @return array
     */
    public function getVideoReportAction()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all();
        $parameterObject = new Parameter($params);
        $filterObject = $parameterObject->getFilterObject();
        $breakDownObject = $parameterObject->getBreakDownObject();

        if ($this->getUser() instanceof PublisherInterface) {
            $publisherId = $this->getUser()->getId();
            $filterObject->setPublisherId([$publisherId]);
        }

        return $this->getResult(
            $this->getReportBuilder()->getReports($filterObject, $breakDownObject)
        );
    }

    /**
     * @return \Tagcade\Service\Report\VideoReport\Selector\VideoReportBuilder
     */
    private function getReportBuilder()
    {
        return $this->get('tagcade.service.report.video_report.selector.video_report_builder');
    }

    /**
     * get Result
     * @param $result
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getResult($result)
    {
        /** @var false|array|ReportCollection $result */
        if ($result === false || (is_array($result) && count($result) < 1)) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $result;
    }
}