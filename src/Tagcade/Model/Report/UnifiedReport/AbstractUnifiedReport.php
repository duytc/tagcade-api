<?php


namespace Tagcade\Model\Report\UnifiedReport;


use Tagcade\Model\Report\CalculateFieldRateTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport as BaseAbstractReport;

abstract class AbstractUnifiedReport extends BaseAbstractReport implements ReportInterface
{
    use CalculateRatiosTrait;
    use CalculateFieldRateTrait;

    protected $id;

    public function forceSetFillRate($fillRate)
    {
        $this->fillRate = $fillRate;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}