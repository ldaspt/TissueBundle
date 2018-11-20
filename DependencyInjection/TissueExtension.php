<?php

/*
 * This file is part of the CLTissueBundle.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Bundle\TissueBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TissueExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->setParameters($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function setParameters(array $config, ContainerBuilder $container)
    {
        if (true !== $config['enabled']) {
            $container->setParameter('tissue.adapter.alias', 'null');

            return;
        }

        foreach ($config['adapter'] as $key => $val) {
            if ('options' === $key) {
                foreach ($val as $k => $v) {
                    $container->setParameter(sprintf('tissue.adapter.options.%s', $k), $v);
                }
                continue;
            }

            $container->setParameter(sprintf('tissue.adapter.%s', $key), $val);
        }
    }
}
