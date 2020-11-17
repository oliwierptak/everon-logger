<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator;

use InvalidArgumentException;
use function array_key_exists;

abstract class AbstractConfigurator
{
    abstract public function fromArray(array $data): AbstractConfigurator;

    abstract public function toArray(): array;

    /**
     * @var \Everon\Logger\Configurator\AbstractPluginConfigurator[]
     */
    protected array $pluginConfiguratorCollection = [];

    /**
     * @return \Everon\Logger\Configurator\AbstractPluginConfigurator[]
     */
    public function getPluginConfiguratorCollection(): array
    {
        return $this->pluginConfiguratorCollection;
    }

    /**
     * @param \Everon\Logger\Configurator\AbstractPluginConfigurator[] $pluginConfiguratorCollection
     *
     * @return $this
     */
    public function setPluginConfiguratorCollection(array $pluginConfiguratorCollection): AbstractConfigurator
    {
        $this->pluginConfiguratorCollection = $pluginConfiguratorCollection;

        return $this;
    }

    public function addPluginConfigurator(AbstractPluginConfigurator $pluginConfigurator): AbstractConfigurator
    {
        $this->pluginConfiguratorCollection[$pluginConfigurator->getPluginClass()] = $pluginConfigurator;

        return $this;
    }

    public function getPluginConfiguratorByPluginName(string $pluginClass): AbstractPluginConfigurator
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
