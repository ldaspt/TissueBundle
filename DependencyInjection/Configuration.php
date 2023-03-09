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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration implements ConfigurationInterface
{
    private const ADAPTER_CLAMAV = 'clamav';
    private const ADAPTER_CLAMAVPHP = 'clamavphp';
    private const DEFAULT_ALIAS = self::ADAPTER_CLAMAV;

    /**
     * @var array
     */
    private const SUPPORTED_ADAPTERS = [self::ADAPTER_CLAMAV, self::ADAPTER_CLAMAVPHP];

    /**
     * @var OptionsResolver[]
     */
    private $resolvers = [];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {

        $treeBuilder = new TreeBuilder('tissue');
        $self = $this;
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('tissue');
        }

        $rootNode
            ->canBeEnabled()
            ->children()
                ->arrayNode('adapter')
                    ->isRequired()
                    ->beforeNormalization()
                        ->ifNull()
                        ->then(function ($v) use ($self) {
                            $retVal = ['alias' => self::DEFAULT_ALIAS];
                            if ($resolver = $self->getResolver($retVal['alias'])) {
                                $retVal['options'] = $resolver->resolve([]);
                            }

                            return $retVal;
                        })
                    ->end()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) use ($self) {
                            $retVal = ['alias' => $v];
                            if ($resolver = $self->getResolver($retVal['alias'])) {
                                $retVal['options'] = $resolver->resolve([]);
                            }

                            return $retVal;
                        })
                    ->end()
                    ->beforeNormalization()
                    ->ifArray()
                    ->then(function ($v) use ($self) {
                        $alias = \array_key_exists('alias', $v) ? ($v['alias'] ?? self::DEFAULT_ALIAS) : self::DEFAULT_ALIAS;
                        $options = \array_key_exists('options', $v) ? $v['options'] : [];

                        $retVal = ['alias' => $alias];
                        if ($resolver = $self->getResolver($retVal['alias'])) {
                            $retVal['options'] = $resolver->resolve($options);
                        }

                        return $retVal;
                    })
                    ->end()
                    ->validate()
                        ->ifArray()
                        ->then(function ($v) {
                            if ('clamavphp' === $v['alias'] && !class_exists('\CL\Tissue\Adapter\ClamAVPHP\ClamAVPHPAdapter')) {
                                throw new InvalidConfigurationException('If you want to use the `clamavphp` adapter, you need to add the `cleentfaar/tissue-clamavphp-adapter` package to your composer.json');
                            }

                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('alias')->isRequired()->defaultValue('clamav')->end()
                        ->variableNode('options')->defaultValue([])->end()
                        // ...
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param string $alias
     *
     * @return OptionsResolver|null
     */
    private function getResolver($alias): ?OptionsResolver
    {
        if (!\in_array($alias, self::SUPPORTED_ADAPTERS, true)) {
            return null;
        }

        if (!isset($this->resolvers[$alias])) {
            $this->resolvers[$alias] = $this->createResolver($alias);
        }

        return $this->resolvers[$alias];
    }

    /**
     * @param string $alias
     *
     * @return OptionsResolver
     */
    private function createResolver($alias): OptionsResolver
    {
        $resolver = new OptionsResolver();
        switch ($alias) {
            case 'clamav':
                $resolver->setDefaults([
                    'bin' => '/usr/bin/clamdscan',
                    'database' => null,
                ]);
                $resolver->addAllowedTypes('bin', 'string');
                $resolver->addAllowedTypes('database', ['string', 'null']);
                break;
            default:
                break;
        }

        return $resolver;
    }
}
