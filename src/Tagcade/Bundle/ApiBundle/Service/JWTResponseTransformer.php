<?php

namespace Tagcade\Bundle\ApiBundle\Service;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class JWTResponseTransformer
{
    public function transform(array $data, UserEntityInterface $user)
    {
        $data['id'] = $user->getId();
        $data['username'] = $user->getUsername();
        $data['userRoles'] = $user->getUserRoles();
        $data['enabledModules'] = $user->getEnabledModules();

        if ($user instanceof PublisherInterface && !$user instanceof SubPublisherInterface) {
            $data['settings'] = $user->getSettings();
            $data['exchanges'] = $user->getExchanges();
        }

        if ($user instanceof SubPublisherInterface) {
            /** @Var SubPublisherInterface $user */
            $data['demandSourceTransparency'] = $user->isDemandSourceTransparency();
            $data['enableViewTagcadeReport'] = $user->isEnableViewTagcadeReport();
        }

        return $data;
    }
}