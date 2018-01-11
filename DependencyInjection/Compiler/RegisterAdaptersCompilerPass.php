<?php

/*
 * This file is part of the CLTissueBundle.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Bundle\TissueBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAdaptersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $registryId = 'CL\Tissue\Util\AdapterRegistry';
        if (!$container->hasDefinition($registryId)) {
            return;
        }

        $this->registerScannerDefinition($container);

        $tagName = 'tissue.adapter';
        $registryDefinition = $container->getDefinition($registryId);
        foreach ($container->findTaggedServiceIds($tagName) as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $registryDefinition->addMethodCall('register', [new Reference($id), $attributes['alias']]);
            }
        }
    }

    /**
     * registerScannerDefinition.
     *
     * @param ContainerBuilder $container
     */
    private function registerScannerDefinition(ContainerBuilder $container)
    {
        $adapterAlias = $container->getParameter('tissue.adapter.alias');

        if (\in_array($adapterAlias, ['clamav', 'mock', 'null'], true)) {
            $class = $container->getParameter(sprintf('tissue.adapter.%s.class', $adapterAlias));
            $args = $this->getAdapterArguments($adapterAlias, $container);

            $chosenDefinition = new Definition($class, $args);
            $chosenDefinition->addTag('tissue.adapter', ['alias' => $adapterAlias]);
            $container->setDefinition('tissue.scanner', $chosenDefinition);
        }
    }

    /**
     * @param string           $adapter
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getAdapterArguments(string $adapter, ContainerBuilder $container): array
    {
        if ('clamav' === $adapter) {
            $args[] = $container->getParameter('tissue.adapter.options.bin');
            $args[] = $container->getParameter('tissue.adapter.options.database');

            return $args;
        }

        return [];
    }
}
