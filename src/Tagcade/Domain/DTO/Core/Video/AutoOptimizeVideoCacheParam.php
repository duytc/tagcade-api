<?php


namespace Tagcade\Domain\DTO\Core\Video;
use Tagcade\Exception\InvalidArgumentException;

class AutoOptimizeVideoCacheParam
{
    const IDENTIFY_BY_ID = 'demandAdTagId';
    const IDENTIFY_BY_NAME = 'demandAdTagName';

    const MAPPED_BY_KEY = 'mappedBy';
    const SCORES_KEY = 'scores';
    const WATERFALL_TAGS_KEY = 'waterfallTags';

    /** @var  string */
    private $mappedBy;

    /** @var  array */
    private $scores;

    /** @var array */
    private $waterfallTags;

    /**
     * AutoOptimizeCacheParam constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        // validate map key
        if (!array_key_exists(self::MAPPED_BY_KEY, $data) || empty($data[self::MAPPED_BY_KEY])) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::MAPPED_BY_KEY));
        }

        $this->mappedBy = $data[self::MAPPED_BY_KEY];

        // validate waterfall tags key
        if (!array_key_exists(self::WATERFALL_TAGS_KEY, $data) || empty($data[self::WATERFALL_TAGS_KEY])) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::WATERFALL_TAGS_KEY));
        }

        $this->waterfallTags = $data[self::WATERFALL_TAGS_KEY];

        // validate score
        if (!array_key_exists(self::SCORES_KEY, $data) || !is_array($data[self::SCORES_KEY]) || empty($data[self::SCORES_KEY])) {
            throw new InvalidArgumentException(sprintf('"%s" should not be an empty array', self::SCORES_KEY));
        }

        $this->scores = $data[self::SCORES_KEY];

        foreach ($this->scores as &$score) {
            try {
                $score = new AutoOptimizeVideoCacheSegmentParam($score);
            } catch (InvalidArgumentException $exception) {
                throw $exception;
            }
        }
    }

    /**
     * @return string
     */
    public function getMappedBy()
    {
        return $this->mappedBy;
    }

    /**
     * @return array
     */
    public function getWaterfallTags(): array
    {
        return $this->waterfallTags;
    }

    /**
     * @return array
     */
    public function getScores()
    {
        return $this->scores;
    }
}