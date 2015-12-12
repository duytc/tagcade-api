<?php


namespace Tagcade\Bundle\ReportApiBundle\Subscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;

class UnifiedReportPaginationSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        $target = $event->target;
        if ($target instanceof CompoundResult) {
            $event->count = $target->getCount();
            $event->items = $target->getItems();
            $event->stopPropagation();
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 1/*increased priority to override any internal*/)
        );
    }
}