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
     * @return \Tagcade\Model\ModelInterface
     * @throws \Exception
     */
    public function post(array $parameters)
    {
        /** @var BaseAdSlotInterface[] $adSlots */
        $adSlots = array_key_exists('adSlot', $parameters) ? $parameters['adSlot']: null;
        if ($adSlots == null) {
            throw new \Exception('Invalid ad slot field');
        }

        $myAdSlots = !is_array($adSlots) ? [$adSlots] : $adSlots;
        if (count($myAdSlots) < 1) {
            throw new \Exception('Expect ad slot field');
        }

        $parameters['adSlot'] = array_shift($myAdSlots)->getId();
        $adTag =[];
        $adTag[0] =  parent::post($parameters);

        if (count($myAdSlots) > 0) { // for multiple ad slots
            $adTagLibraryId = $this->getDomainManager()->makeStandAlone($adTag[0]->getId());
            $adTag[] = $this->adTagGenerator->generateAdTagForMultiAdSlots($adTagLibraryId, $myAdSlots);
        }

        return $adTag;
    }
}