<?php


namespace Tagcade\Domain\DTO\Core;
use Tagcade\Exception\InvalidArgumentException;

class AutoOptimizeCacheSlotParam
{
    const IDENTIFIER_KEY = 'identifier';
    const SCORE_KEY = 'score';
    const AD_SLOT_ID_KEY = 'adSlotId';
    const AD_TAG_SCORES_KEY = 'adTagScores';

    /** @var  int */
    private $adSlotId;

    /** @var  array */
    private $adTagScores;

    /**
     * AutoOptimizeCacheSlotParam constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!array_key_exists(self::AD_SLOT_ID_KEY, $data)) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::AD_SLOT_ID_KEY));
        }

        $this->adSlotId = $data[self::AD_SLOT_ID_KEY];

        if (!array_key_exists(self::AD_TAG_SCORES_KEY, $data)) {
            throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::AD_TAG_SCORES_KEY));
        }

        $this->adTagScores = $data[self::AD_TAG_SCORES_KEY];

        if (!is_array($this->adTagScores) || empty($this->adTagScores)) {
            throw new InvalidArgumentException(sprintf('"%s" should not be an empty array', self::AD_TAG_SCORES_KEY));
        }

        foreach ($this->adTagScores as $adTagScore) {
            if (!array_key_exists(self::IDENTIFIER_KEY, $adTagScore)) {
                throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::IDENTIFIER_KEY));
            }

            if (!array_key_exists(self::SCORE_KEY, $adTagScore)) {
                throw new InvalidArgumentException(sprintf('"%s" should not be empty', self::SCORE_KEY));
            }
        }
    }

    /**
     * @return int
     */
    public function getAdSlotId(): int
    {
        return $this->adSlotId;
    }

    /**
     * @return array
     */
    public function getAdTagScores(): array
    {
        return $this->adTagScores;
    }
}