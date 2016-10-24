<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;


class ReportTransform
{
    const VISIT_KEY = 'visits';
    const VIDEO_AD_IMPRESSIONS_KEY = 'videoAdImpressions';
    const BILLED_AMOUNT_KEY = 'billedAmount';
    const BILLED_RATE_KEY = 'billedRate';
    const AVERAGE_VISITS_KEY = 'averageVisits';
    const AVERAGE_VIDEO_AD_IMPRESSIONS_KEY = 'averageVideoAdImpressions';
    const AVERAGE_BILLED_AMOUNT_KEY = 'averageBilledAmount';
    const DATE_KEY = 'date';
    const SITE_KEY = 'site';
    const PUBLISHER_KEY = 'publisher';

    public static function convert(array $reports){
        for ($i = 0; $i < count($reports); $i++) {
            foreach ($reports[$i] as $key => $value) {
                if ($key == self::VISIT_KEY) {
                    $reports[$i][$key] = intval($value);
                }

                if ($key == self::VIDEO_AD_IMPRESSIONS_KEY) {
                    $reports[$i][$key] = intval($value);
                }

                if ($key == self::BILLED_AMOUNT_KEY) {
                    $reports[$i][$key] = floatval($value);
                }

                if ($key == self::BILLED_RATE_KEY) {
                    $reports[$i][$key] = floatval($value);
                }

                if ($key == self::AVERAGE_VISITS_KEY) {
                    $reports[$i][$key] = intval($value);
                }

                if ($key == self::AVERAGE_VIDEO_AD_IMPRESSIONS_KEY) {
                    $reports[$i][$key] = intval($value);
                }

                if ($key == self::AVERAGE_BILLED_AMOUNT_KEY) {
                    $reports[$i][$key] = floatval($value);
                }

                if ($key == self::DATE_KEY) {
                    $reports[$i][$key] = $value;
                }

                if ($key == self::SITE_KEY) {
                    $reports[$i][$key] = $value;
                }

                if ($key == self::PUBLISHER_KEY) {
                    $reports[$i][$key] = $value;
                }
            }
        }
        return $reports;
    }
}