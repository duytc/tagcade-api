<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Rollerworks\Bundle\MultiUserBundle\DependencyInjection\Compiler\RegisterFosUserMappingsPass;

class TagcadeUserSystemSubPublisherBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(RegisterFosUserMappingsPass::createOrmMappingDriver('tagcade_user_system_sub_publisher'));
    }
}
