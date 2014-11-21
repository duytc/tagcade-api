<?php

// Used only for importing legacy data into the new report table format
// Not used for any other purpose

use Tagcade\Service\Report\PerformanceReport\Display\Counter\AbstractEventCounter;

class PdoEventCounter extends AbstractEventCounter
{
    const DB_DATE = 'Y-m-d';

    /**
     * @var PDO
     */
    private $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * @inheritdoc
     */
    public function getSlotOpportunityCount($slotId)
    {
        if (!is_numeric($slotId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT opportunities FROM reports_ad_slots where date = ? and ad_slot_id = ?");
        $sth->execute([$this->getDbDate(), $slotId]);

        return $sth->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityCount($tagId)
    {
        if (!is_numeric($tagId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT opportunities FROM reports_ad_tags where date = ? and ad_tag_id = ?");
        $sth->execute([$this->getDbDate(), $tagId]);

        return $sth->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCount($tagId)
    {
        if (!is_numeric($tagId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT impressions FROM reports_ad_tags where date = ? and ad_tag_id = ?");
        $sth->execute([$this->getDbDate(), $tagId]);

        return $sth->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount($tagId)
    {
        if (!is_numeric($tagId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT fallback_impressions FROM reports_ad_tags where date = ? and ad_tag_id = ?");
        $sth->execute([$this->getDbDate(), $tagId]);

        return $sth->fetchColumn();
    }

    protected function getDbDate() {
        return $this->date->format(self::DB_DATE);
    }
}