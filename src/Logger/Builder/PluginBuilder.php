<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Configurator\AbstractConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Exception\PluginBuildException;
use Throwable;

class PluginBuilder
{
    /**
     * @param \Everon\Logger\Configurator\AbstractConfigurator $pluginConfigurator
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     * @throws \Everon\Logger\Exception\PluginBuildException
     */
    public function buildPlugin(AbstractConfigurator $pluginConfigurator): LoggerPluginInterface
    {
        try {
            $pluginFactoryClass = $pluginConfigurator->getPluginFactoryClass();
            if ($pluginFactoryClass !== null && class_exists($pluginFactoryClass)) {
                return (new $pluginFactoryClass())->create($pluginConfigurator);
            }

            $pluginClassName = $pluginConfigurator->getPluginClass();

            return new $pluginClassName($pluginConfigurator);
        }
        catch (Throwable $exception) {
            throw new PluginBuildException(sprintf(
                'Could not build plugin: "%s". Error: %s',
                $pluginConfigurator,
                $exception->getMessage()
            ), $exception->getCode(), $exception);
        }
    }
}
