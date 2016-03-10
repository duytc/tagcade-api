<?php


namespace Tagcade\Service\Core\Exchange;


use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\User\Role\PublisherInterface;

class ExchangeCacheUpdater implements ExchangeCacheUpdaterInterface
{
    const ABBREVIATION_KEY = 'abbreviation';
    /**
     * @var PublisherManagerInterface
     */
    private $publisherManager;
    /**
     * @var array
     */
    private $exchanges;

    /**
     * ExchangeCacheUpdater constructor.
     * @param PublisherManagerInterface $publisherManager
     * @param $exchanges
     */
    public function __construct(PublisherManagerInterface $publisherManager, $exchanges)
    {
        $this->publisherManager = $publisherManager;
        if ($exchanges === null) {
            $this->exchanges = [];
        }

        $this->exchanges = array_map(function(array $exchange) {
            return $exchange[self::ABBREVIATION_KEY];
        }, $exchanges);
    }

    /**
     * @inheritdoc
     */
    public function updateCacheAfterUpdateExchangeParameter($oldName, $newName = null)
    {
        if (!in_array($oldName, $this->exchanges) && $newName === null) {
            throw new RuntimeException(sprintf('exchange "%s" is not existed', $oldName));
        }

        if ($newName !== null && !in_array($newName, $this->exchanges)) {
            throw new RuntimeException(sprintf('exchange "%s" is not existed', $newName));
        }

        $publishers = $this->publisherManager->allActivePublishers();
        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new RuntimeException('That publisher does not exist');
            }

            if ($publisher->hasRtbModule()) {
                /** @var array $publisherExchanges */
                $publisherExchanges = $publisher->getExchanges();
                $this->getUpdatedExchangesList($publisherExchanges, $oldName, $newName);

                $publisher->setExchanges($publisherExchanges);
                $this->publisherManager->save($publisher);
            }
        }
    }

    /**
     * @param array $listExchange
     * @param $oldName
     * @param null $newName
     */
    private function getUpdatedExchangesList(array &$listExchange, $oldName, $newName = null)
    {
        $key = array_search($oldName, $listExchange);
        if ($key === FALSE) {
            return;
        }

        if ($newName !== null) {
            $listExchange[$key] = $newName;
            return;
        }

        unset($listExchange[$key]);
        $listExchange = array_values($listExchange);
    }
}