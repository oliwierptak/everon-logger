<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;

class PluginBuilder
{
    /**
     * @param \Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator $configurator
     * @param string $pluginClass
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     */
    public function buildPlugin(LoggerPluginConfigurator $configurator, string $pluginClass): LoggerPluginInterface
    {
        $pluginFactoryClass = $this->extractPluginFactoryClass($pluginClass);
        if ($pluginFactoryClass !== null && class_exists($pluginFactoryClass)) {
            return (new $pluginFactoryClass)->create();
        }

        $pluginConfigurator = $this->extractPluginConfigurator($configurator, $pluginClass);

        return new $pluginClass($pluginConfigurator);
    }

    protected function extractPluginFactoryClass(string $pluginClass): ?string
    {
        $tokens = explode('\\', $pluginClass);
        array_pop($tokens);
        $pluginFactoryClass = implode('\\', $tokens) . 'Factory';

        $result = null;
        if (class_exists($pluginFactoryClass)) {
            $result = $pluginFactoryClass;
        }

        return $result;
    }

    /**
     * @param \Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator $configurator
     * @param string $pluginClass
     *
     * @return mixed
     */
    protected function extractPluginConfigurator(LoggerPluginConfigurator $configurator, string $pluginClass)
    {
        $namespaceTokens = explode('\\', $pluginClass);
        $pluginName = array_pop($namespaceTokens);

        $tokens = preg_split('/(?<=[^A-Z])(?=[A-Z])/', $pluginName);
        $pluginName = array_shift($tokens);

        $getter = sprintf('get%sConfigurator', ucfirst($pluginName));

        return $configurator->$getter();
    }
}
