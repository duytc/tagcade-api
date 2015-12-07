<?php

namespace Tagcade\Model\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\Report\UnifiedReport\UnifiedReportModelInterface;

interface PulsePointUnifiedReportModelInterface extends UnifiedReportModelInterface
{
    /**
     * @return float
     */
    public function getFillRate();
    /**
     * @param float $fillRate
     * @return self
     */
    public function setFillRate($fillRate);

    /**
     * @return float
     */
    public function getPaidImps();

    /**
     * @param float $paidImps
     * @return self
     */
    public function setPaidImps($paidImps);

    /**
     * @return float
     */
    public function getTotalImps();
    /**
     * @param float $totalImps
     * @return self
     */
    public function setTotalImps($totalImps);
} 