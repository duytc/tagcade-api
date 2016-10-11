<?php


namespace Tagcade\Service\Core\VideoDemandAdTag;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Behaviors\ValidateVideoDemandAdTagAgainstPlacementRuleTrait;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;

class AutoPauseService implements AutoPauseServiceInterface
{
    use ValidateVideoDemandAdTagAgainstPlacementRuleTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AutoPauseService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function autoPauseDemandAdTags(array $demandAdTags)
    {
        $count = 0;
        foreach ($demandAdTags as $demandAdTag) {
            if (!$demandAdTag instanceof LibraryVideoDemandAdTagInterface) {
                throw new InvalidArgumentException('expect LibraryVideoDemandAdTagInterface object');
            }

            $count += $this->autoPauseDemandAdTag($demandAdTag);
        }

        return $count;
    }


    protected function autoPauseDemandAdTag(LibraryVideoDemandAdTagInterface $demandAdTag)
    {
        $count = 0;
        $demandAdTags = $demandAdTag->getVideoDemandAdTags();
        /** @var VideoDemandAdTagInterface $tag */
        foreach ($demandAdTags as $tag) {
            if ($this->validateDemandAdTagAgainstPlacementRule($tag) === false) {
                $tag->setActive(VideoDemandAdTag::AUTO_PAUSED);
                $this->em->merge($tag);
                $count++;
            }
        }

        $this->em->flush();

        return $count;
    }
}