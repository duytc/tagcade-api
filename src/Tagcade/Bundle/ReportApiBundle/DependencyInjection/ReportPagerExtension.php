<?php


namespace Tagcade\Bundle\ReportApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ReportPagerExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration( $configuration, $config );

        $container->setParameter( 'knp_paginator.page_range', $processedConfig[ 'page_range' ]);
    }
}