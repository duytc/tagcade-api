<?php


namespace Tagcade\Worker\Workers;


use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use stdClass;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Repository\Core\VideoDemandAdTagRepositoryInterface;
use Tagcade\Service\Core\VideoDemandAdTag\AutoPauseServiceInterface;

class AutoPauseVideoDemandAdTagWorker
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var VideoDemandAdTagRepositoryInterface
     */
    private $videoDemandAdTagRepository;

    /**
     * @var AutoPauseServiceInterface
     */
    private $autoPauseService;

    function __construct(EntityManagerInterface $em, AutoPauseServiceInterface $autoPauseService)
    {
        $this->em = $em;
        $this->videoDemandAdTagRepository = $this->em->getRepository(VideoDemandAdTag::class);
        $this->autoPauseService = $autoPauseService;
    }

    /**
     * @param stdClass $param
     */
    public function autoPauseVideoDemandAdTag(StdClass $param)
    {
        $tags = [];
        $videoDemandAdTags = $param->videoDemandAdTags;

        if (!is_array($videoDemandAdTags)) {
            throw new InvalidArgumentException(sprintf('Video ad tag expected an array, got type %s', gettype($videoDemandAdTags)));
        }

        foreach($videoDemandAdTags as $videoDemandAdTag) {
            $tag = $this->videoDemandAdTagRepository->find($videoDemandAdTag);
            if ($tag instanceof VideoDemandAdTagInterface) {
                $tags[] = $tag;
            }
        }

        $this->autoPauseService->autoPauseDemandAdTags($tags);
    }
}