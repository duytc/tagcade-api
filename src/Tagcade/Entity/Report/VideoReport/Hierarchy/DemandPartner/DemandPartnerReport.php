<?php

namespace Tagcade\Entity\Report\VideoReport\Hierarchy\DemandPartner;

use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport as DemandPartnerReportModel;

class DemandPartnerReport extends DemandPartnerReportModel
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
    protected $videoDemandPartner;
    protected $subReports;

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