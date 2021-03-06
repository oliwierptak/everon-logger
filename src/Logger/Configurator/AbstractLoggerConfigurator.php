<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator;

use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;
use InvalidArgumentException;
use function array_key_exists;

abstract class AbstractLoggerConfigurator implements LoggerConfiguratorInterface
{
    /**
     * @var \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[]
     */
    protected array $pluginConfiguratorCollection = [];

    /**
     * [pluginClassName => configurator]
     *
     * @return \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[]
     */
    public function getPluginConfiguratorCollection(): array
    {
        return $this->pluginConfiguratorCollection;
    }

    /**
     * [pluginClass => configurator]
     *
     * @param \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[] $pluginConfiguratorCollection
     *
     * @return \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
     */
    public function setPluginConfiguratorCollection(array $pluginConfiguratorCollection): LoggerConfiguratorInterface
    {
        $this->pluginConfiguratorCollection = $pluginConfiguratorCollection;

        return $this;
    }

    public function addPluginConfigurator(PluginConfiguratorInterface $pluginConfigurator): LoggerConfiguratorInterface
    {
        $this->pluginConfiguratorCollection[$pluginConfigurator->getPluginClass()] = $pluginConfigurator;

        return $this;
    }

    public function getConfiguratorByPluginName(string $pluginClass): PluginConfiguratorInterface
    {
        if (!array_key_exists($pluginClass, $this->pluginConfiguratorCollection)) {
            throw new InvalidArgumentException(sprintf(
                'Could not find plugin configurator for plugin: "%s"',
                $pluginClass
            ));
        }

        return $this->pluginConfiguratorCollection[$pluginClass];
    }
}
