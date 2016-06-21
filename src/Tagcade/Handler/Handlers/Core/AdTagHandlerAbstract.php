<?php

namespace Tagcade\Handler\Handlers\Core;

use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Form\Type\RoleSpecificFormTypeInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\TagLibrary\AdTagGeneratorInterface;

abstract class AdTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @var AdTagGeneratorInterface
     */
    private $adTagGenerator;

    public function __construct(FormFactoryInterface $formFactory, RoleSpecificFormTypeInterface $formType, $domainManager, UserRoleInterface $userRole = null, AdTagGeneratorInterface $adTagGenerator)
    {
        parent::__construct($formFactory, $formType, $domainManager,$userRole);
        $this->adTagGenerator = $adTagGenerator;
    }

    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AdTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * @param array $parameters
     * @return \Tagcade\Model\ModelInterface|void
     */
    public function post(array $parameters)
    {
        /** @var BaseAdSlotInterface[] $adSlots */
        $adSlots = array_key_exists('adSlots', $parameters) ? $parameters['adSlots']: null;

        if (array_key_exists('adSlots', $parameters) && count($adSlots) > 0) {
            $parameters['adSlot'] = $adSlots[0]->getId();
            unset ($parameters['adSlots']);
            unset($adSlots[0]);
        }

        $adTag =  parent::post($parameters);

        if (count($adSlots) > 0) { // for multiple ad slots
            $adTagLibraryId = $this->getDomainManager()->makeStandAlone($adTag);
            $this->adTagGenerator->generateAdTagFromMultiAdSlot($adTagLibraryId, $adSlots);
        }

        return $adTag;
    }

}