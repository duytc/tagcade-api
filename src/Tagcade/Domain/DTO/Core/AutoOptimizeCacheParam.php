<?php


namespace Tagcade\Domain\DTO\Core;
use Tagcade\Exception\InvalidArgumentException;

class AutoOptimizeCacheParam
{
    const IDENTIFY_BY_ID = 'adTagId';
    const IDENTIFY_BY_NAME = 'adTagName';

    const MAPPED_BY_KEY = 'mappedBy';
    const SCORES_KEY = 'scores';
    const AD_SLOTS_KEY = 'adSlots';

    /** @var  string */
    private $mappedBy;

    /** @var  array */
    private $scores;

    /** @var array */
    private $adSlots;

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

        // validate adSlots key
        if (!array_key_exists(self::AD_SLOTS_KEY, $data) || empty($data[self::AD_SLOTS_KEY])) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::AD_SLOTS_KEY));
        }

        $this->adSlots = $data[self::AD_SLOTS_KEY];

        // validate score
        if (!array_key_exists(self::SCORES_KEY, $data) || !is_array($data[self::SCORES_KEY]) || empty($data[self::SCORES_KEY])) {
            throw new InvalidArgumentException(sprintf('"%s" should not be an empty array', self::SCORES_KEY));
        }

        $this->scores = $data[self::SCORES_KEY];

        foreach ($this->scores as &$score) {
            try {
                $score = new AutoOptimizeCacheSegmentParam($score);
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
    public function getAdSlots(): array
    {
        return $this->adSlots;
    }

    /**
     * @return array
     */
    public function getScores()
    {
        return $this->scores;
    }
}