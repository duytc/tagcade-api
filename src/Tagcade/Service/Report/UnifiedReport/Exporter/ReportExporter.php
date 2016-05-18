<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

class ReportExporter implements ReportExporterInterface
{
    /**
     * @var ExporterInterface[]
     */
    protected $exporters;

    /**
     * ReportSelector constructor.
     * @param ExporterInterface[] $exporters
     */
    public function __construct(array $exporters)
    {
        $this->exporters = [];

        foreach ($exporters as $exporter) {
            if ($exporter instanceof ExporterInterface) {
                $this->exporters = $exporters;
            }
        }
    }

    public function export(ReportTypeInterface $reportType, Params $params)
    {
        $exporter = $this->getExporterFor($reportType);
        if (!$exporter instanceof ExporterInterface) {
            throw new LogicException(sprintf('expect UnifiedExporterInterface, %s given', get_class($exporter)));
        }

        $exportedData = $exporter->export($reportType, $params);

        if (!is_array($exportedData) || empty($exportedData)) {
            return false;
        }

        return $this->exportCreator->create($exportedData);
    }
}