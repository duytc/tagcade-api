<?php


namespace Tagcade\Service\TagLibrary;


use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTag;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform\AdSlotInterface;

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
     * @param $adTagLibrary
     * @param array $adSlots
     */

    public function generateAdTagFromMultiAdSlot($adTagLibrary, array $adSlots)
    {
        if (!$adTagLibrary instanceof LibraryAdTag) {
            throw new \InvalidArgumentException('Invalid the first parameter, expect ad tag library type');
        }

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
        }
    }
} 