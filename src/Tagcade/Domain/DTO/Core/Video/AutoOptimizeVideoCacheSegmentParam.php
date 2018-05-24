<?php


namespace Tagcade\Domain\DTO\Core\Video;
use Tagcade\Exception\InvalidArgumentException;

class AutoOptimizeVideoCacheSegmentParam
{
    const SEGMENT_FIELDS_KEY = 'segmentFields';
    const DEMAND_TAG_SCORES_KEY = 'demandTagScores';

    /** @var  string */
    private $segmentFields;

    /** @var array */
    private $demandTagScores;

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

        if (!array_key_exists(self::DEMAND_TAG_SCORES_KEY, $data)) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::DEMAND_TAG_SCORES_KEY));
        }

        $this->demandTagScores = $data[self::DEMAND_TAG_SCORES_KEY];
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
    public function getDemandTagScores(): array
    {
        return $this->demandTagScores;
    }
}