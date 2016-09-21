<?php


namespace Tagcade\Service\Report\VideoReport\Selector;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Transformers\TransformerInterface;

class VideoReportTransformer implements VideoReportTransformerInterface
{
    /** @var array|TransformerInterface[] */
    protected $videoReportTransformers = [];

    /**
     * VideoReportTransformer constructor.
     * @param array|TransformerInterface[] $videoReportTransformers
     */
    public function __construct(array $videoReportTransformers)
    {
        foreach ($videoReportTransformers as $vrt) {
            if ($vrt instanceof TransformerInterface) {
                $this->videoReportTransformers[] = $vrt;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function transformReport(array $reports, ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        // get transformer
        $transformer = $this->getTransformerFor($reportType, $breakDownParameter,$filterParameter);

        // do transform
        return $transformer->transform($reports);
    }


    /**
     * get Transformer For a reportType
     *
     * @param ReportTypeInterface $reportType
     * @param BreakDownParameterInterface $breakDownParameter
     * @param FilterParameterInterface $filterParameter
     * @throws \Exception
     * @return TransformerInterface
     */
    protected function getTransformerFor(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        /** @var TransformerInterface $transformer */
        foreach ($this->videoReportTransformers as $transformer) {
            if ($transformer->supportsReportTypeAndBreakdown($reportType, $breakDownParameter,$filterParameter)) {
                return $transformer;
            }
        }

        throw new \Exception(sprintf('Not found transformer for that report type %s and breakdown ', $reportType->getReportType()));
    }

    public function extractParentId($formattedResult)
    {

    }
}