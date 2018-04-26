<?php


namespace Tagcade\Domain\DTO\Core;
use Tagcade\Exception\InvalidArgumentException;

class AutoOptimizeCacheSegmentParam
{
    const SEGMENT_FIELDS_KEY = 'segmentFields';
    const AD_TAG_SCORES_KEY = 'adTagScores';

    /** @var  string */
    private $segmentFields;

    /** @var array */
    private $adTagScores;

    /**
     * AutoOptimizeCacheSegmentParam constructor.
     * * @param array $data
     */
    public function __construct(array $data)
    {
        // IMPORTANT: allow segments empty, so that we know the scores is global (default) score for no segment
        // TODO: remove when stable...
        // if (!array_key_exists(self::SEGMENT_FIELDS_KEY, $data)) {
        //     throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::SEGMENT_FIELDS_KEY));
        // }

        $this->segmentFields = array_key_exists(self::SEGMENT_FIELDS_KEY, $data) ? $data[self::SEGMENT_FIELDS_KEY] : [];
        ksort($this->segmentFields);

        if (!array_key_exists(self::AD_TAG_SCORES_KEY, $data)) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::AD_TAG_SCORES_KEY));
        }

        $this->adTagScores = $data[self::AD_TAG_SCORES_KEY];
    }

    /**
     * @return string
     */
    public function getSegmentFields()
    {
        return $this->segmentFields;
    }

    /**
     * @return array
     */
    public function getAdTagScores(): array
    {
        return $this->adTagScores;
    }
}