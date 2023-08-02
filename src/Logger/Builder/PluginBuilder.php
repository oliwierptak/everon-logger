<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Exception\PluginBuildException;
use Throwable;

class PluginBuilder
{
    /**
     * @param \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface $pluginConfigurator
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     * @throws \Everon\Logger\Exception\PluginBuildException
     */
    public function buildPlugin(PluginConfiguratorInterface $pluginConfigurator): LoggerPluginInterface
    {
        try {
            /** @var \Everon\Logger\Contract\Plugin\PluginFactoryInterface $pluginFactoryClass */
            $pluginFactoryClass = $pluginConfigurator->getPluginFactoryClass();
            if ($pluginFactoryClass !== null) {
                return (new $pluginFactoryClass())->create($pluginConfigurator);
            }

            /* @phpstan-ignore-next-line */
            $pluginClassName = $pluginConfigurator->requirePluginClass();

            return new $pluginClassName($pluginConfigurator);
        }
        catch (Throwable $exception) {
            throw new PluginBuildException(sprintf(
                'Could not build plugin: "%s". Error: %s',
                $pluginConfigurator->requirePluginClass(),
                $exception->getMessage()
            ), $exception->getCode(), $exception);
        }
    }
}
