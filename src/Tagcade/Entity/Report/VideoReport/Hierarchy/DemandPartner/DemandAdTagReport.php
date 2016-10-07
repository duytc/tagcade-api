<?php


namespace Tagcade\Entity\Report\VideoReport\Hierarchy\DemandPartner;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandAdTagReport as DemandAdTagReportModel;

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
    protected $fillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $blocks;

    /** @var VideoDemandAdTagInterface */
    protected $videoDemandAdTag;

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