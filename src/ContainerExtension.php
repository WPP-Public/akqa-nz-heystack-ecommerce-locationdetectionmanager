<?php

namespace Heystack\LocationDetection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Heystack\LocationDetection
 */
class ContainerExtension extends Extension
{

    /**
     * Loads a specific configuration. Additionally calls processConfig, which handles overriding
     * the subsytem level configuration with more relevant mysite/config level configuration
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        (new YamlFileLoader(
            $container,
            new FileLocator(ECOMMERCE_LOCATION_DETECTION_BASE_PATH . '/config/')
        ))->load('services.yml');

        $config = (new Processor())->processConfiguration(
            new ContainerConfig(),
            $configs
        );
        
        $def = $container->getDefinition('locale_manager');
        
        $args = $def->getArguments();
        $args[2] = new Reference($config['locale_detector']);
        $def->setArguments($args);
        
        if (isset($config['cookie_name'])) {
            $def->addMethodCall('setCookieName', [$config['cookie_name']]);
        }
        if (isset($config['cookie_expiry'])) {
            $def->addMethodCall('setCookieExpiry', [$config['cookie_expiry']]);
        }
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
        return 'locale_detection';
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'locale_detection';
    }
}
