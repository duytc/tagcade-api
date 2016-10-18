<?php

namespace Tagcade\Entity\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PlatformReport as VideoPlatformReportModel;

class PlatformReport  extends VideoPlatformReportModel
{
    protected $id;
    protected $date;
    protected $requests;
    protected $bids;
    protected $bidRate;
    protected $errors;
    protected $errorRate;
    protected $impressions;
    protected $fillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $billedAmount;
    protected $billedRate;
    protected $blocks;
    protected $adTagRequests;
    protected $adTagBids;
    protected $adTagErrors;
}