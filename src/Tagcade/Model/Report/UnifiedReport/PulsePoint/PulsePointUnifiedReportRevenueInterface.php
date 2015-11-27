<?php

namespace Tagcade\Model\Report\UnifiedReport\PulsePoint;


interface PulsePointUnifiedReportRevenueInterface extends PulsePointUnifiedReportModelInterface
{
    /**
     * @return mixed
     */
    public function getRevenue();

    /**
     * @param mixed $revenue
     * @return self
     */
    public function setRevenue($revenue);

    /**
     * @return mixed
     */
    public function getBackupImpression();

    /**
     * @param mixed $backupImpression
     * @return self
     */
    public function setBackupImpression($backupImpression);

    /**
     * @return mixed
     */
    public function getAvgCpm();

    /**
     * @param mixed $avgCpm
     * @return self
     */
    public function setAvgCpm($avgCpm);
} 