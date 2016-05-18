<?php


namespace Tagcade\Model\Report\UnifiedReport\Publisher;


use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherReport extends AbstractUnifiedReport implements SubPublisherReportInterface
{
    use PartnerReportFields;
    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    /**
     * @param SubPublisherInterface $subPublisher
     * @return $this
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;

        return $this;
    }

    protected function setDefaultName()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            $this->setName($this->subPublisher->getId());
        }
    }

    public function getName()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getUser()->getUsername();
        }

        return '';
    }
}