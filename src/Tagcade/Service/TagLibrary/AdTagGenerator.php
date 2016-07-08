<?php


namespace Tagcade\Service\TagLibrary;


use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTag;
use Tagcade\Model\Core\LibraryAdTagInterface;

class AdTagGenerator implements AdTagGeneratorInterface {
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    /**
     * @param AdTagManagerInterface $adTagManager
     */
    function __construct(AdTagManagerInterface $adTagManager)
    {
        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function generateAdTagForMultiAdSlots(LibraryAdTagInterface $adTagLibrary, array $adSlots)
    {
        $adTags = [];
        foreach ($adSlots as $adSlot) {
            if (!$adSlot instanceof BaseAdSlotInterface) {
                throw new \InvalidArgumentException('Invalid the second parameter, expect ad slot');
            }
        }

        foreach ($adSlots as $adSlot) {
            $adTagObj = new AdTag();
            $adTagObj->setLibraryAdTag($adTagLibrary);
            $adTagObj->setActive(true);
            $adTagObj->setAdSlot($adSlot);

            $this->adTagManager->save($adTagObj);
            $adTags[] = $adTagObj;
        }

        return $adTags;
    }
} 