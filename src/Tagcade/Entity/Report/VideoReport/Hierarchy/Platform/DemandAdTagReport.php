<?php

namespace Tagcade\Entity\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReport as DemandAdTagReportModel;

class DemandAdTagReport extends DemandAdTagReportModel
{
    protected $id;
    protected $date;
    protected $requests;
    protected $bids;
    protected $bidRate;
    protected $errors;
    protected $errorRate;
    protected $impressions;
    protected $requestFillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $blocks;
    protected $videoDemandAdTag;
    protected $estDemandRevenue;

    // Only for display on UI
    protected $adTagRequests;
    protected $adTagBids;
    protected $adTagErrors;

    /**
     * @return mixed
     */
    public function getAdTagBids()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getAdTagErrors()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getAdTagRequests()
    {
        return null;
    }
} 