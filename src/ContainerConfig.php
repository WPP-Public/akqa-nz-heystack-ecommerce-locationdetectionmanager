<?php

namespace Heystack\LocationDetection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Heystack\LocationDetection
 */
class ContainerConfig implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('locale_detection');
        
        $rootNode
            ->children()
                ->scalarNode('locale_detector')->isRequired()->end()
                ->scalarNode('cookie_name')->end()
                ->scalarNode('cookie_expiry')->end()
            ->end();

        return $treeBuilder;
    }
}
