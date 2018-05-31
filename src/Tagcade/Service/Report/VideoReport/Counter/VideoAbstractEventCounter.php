<?php


namespace Tagcade\Service\Report\VideoReport\Counter;


abstract class VideoAbstractEventCounter implements VideoEventCounterInterface
{
    const KEY_DATE_FORMAT = 'ymd';
    const KEY_DATE_HOUR_FORMAT = 'ymdH';
    /* namespace keys */
    const NAMESPACE_WATERFALL_AD_TAG = 'waterfall_tag_%s'; // USING UUID AS ID for video ad tag
    const NAMESPACE_DEMAND_AD_TAG = 'demand_tag_%d'; // using normal id for video ad source
    /**
     * @var \DateTime
     */
    protected $date;
    protected $formattedDate;
    protected $dataWithDateHour;
    /**
     * @inheritdoc
     */
    public function setDate(\DateTime $date = null)
    {
        $today = new \DateTime('today');
        if (!$date) {
            $date = $today;
        }

        if ($date->format('Y-m-d') > $today->format('Y-m-d')) {
            $date = $today;
        } else {
            $this->date = $date;
        }

        $this->formattedDate = !$this->getDataWithDateHour() ? $date->format(self::KEY_DATE_FORMAT) : $date->format(self::KEY_DATE_HOUR_FORMAT);
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        if (!$this->date) {
            $this->date = new \DateTime('today');
        }

        return $this->date;
    }

    public function setDataWithDateHour($dataWithDateHour)
    {
        $this->dataWithDateHour = $dataWithDateHour;
    }

    public function getDataWithDateHour()
    {
        return $this->dataWithDateHour;
    }

    /**
     * get Namespace from namespaceFormat and id, optional with appendingFormat and appendingId
     *
     * @param $namespaceFormat
     * @param $id
     * @return string
     */
    public function getNamespace($namespaceFormat, $id)
    {
        return sprintf($namespaceFormat, $id);
    }

    /**
     * @inheritdoc
     */
    public function getCacheKey($key, $namespace)
    {
        $keyFormat = '%s:%s:%s';
        return sprintf($keyFormat, $key, $namespace, $this->formattedDate);
    }
}