<?php


namespace Tagcade\Service\Report\VideoReport\Counter;


abstract class VideoAbstractEventCounter implements VideoEventCounterInterface
{
    const KEY_DATE_FORMAT = 'ymd';
    /* namespace keys */
    const NAMESPACE_AD_TAG = 'adtag_%s'; // USING UUID AS ID for video ad tag
    const NAMESPACE_AD_SOURCE = 'adsource_%d'; // using normal id for video ad source
    /**
     * @var \DateTime
     */
    protected $date;
    protected $formattedDate;
    /**
     * @inheritdoc
     */
    public function setDate(\DateTime $date = null)
    {
        $today = new \DateTime('today');
        if (!$date) {
            $date = $today;
        }

        if ($date > $today) {
            $date = $today;
        } else {
            $this->date = $date;
        }

        $this->formattedDate = $date->format(self::KEY_DATE_FORMAT);
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