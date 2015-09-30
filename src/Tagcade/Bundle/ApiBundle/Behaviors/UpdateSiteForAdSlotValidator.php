<?php


namespace Tagcade\Bundle\ApiBundle\Behaviors;


use Symfony\Component\HttpFoundation\Request;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BaseAdSlotInterface;

trait UpdateSiteForAdSlotValidator {

    /**
     * check if the updating site is changed
     * @param Request $request
     * @param BaseAdSlotInterface $adSlot
     */
    protected function validateSiteWhenUpdatingAdSlot(Request $request, BaseAdSlotInterface $adSlot)
    {
        if(array_key_exists('site', $request->request->all())) {
            $site = (int)$request->get('site');
            if($adSlot->getSite()->getId() != $site) {
                throw new InvalidArgumentException('site is invalid');
            }
        }
    }
}