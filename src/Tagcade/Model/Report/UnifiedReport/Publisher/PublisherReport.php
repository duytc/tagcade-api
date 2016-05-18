<?php


namespace Tagcade\Model\Report\UnifiedReport\Publisher;


use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
class PublisherReport extends AbstractUnifiedReport implements PublisherReportInterface
{
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    public function getPublisherId()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    protected function setDefaultName()
    {
        if ($this->publisher instanceof PublisherInterface) {
            $this->setName($this->publisher->getFirstName());
        }
    }

    public function getName()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getUser()->getUsername();
        }

        return '';
    }


    /**
     * @return float
     */
    protected function calculateFillRate()
    {
        // TODO: Implement calculateFillRate() method.
    }
}