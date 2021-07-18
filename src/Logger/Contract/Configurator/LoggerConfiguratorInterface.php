<?php

namespace Everon\Logger\Contract\Configurator;

interface LoggerConfiguratorInterface extends ArrayableInterface
{
    /**
     * Specification:
     * - Set the value to true to validate builder's configuration
     *
     * @param bool $validateConfiguration
     *
     * @return \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
     */
    public function setValidateConfiguration(bool $validateConfiguration): LoggerConfiguratorInterface;

    /**
     * Specification:
     * - Return true to validate builder's configuration
     *
     * @return bool
     */
    public function validateConfiguration(): bool;

    /**
     * Specification:
     * - Check value and throw exception if value is null.
     * - Return logger's name
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function requireName(): string;

    /**
     * Specification:
     * - Check value and throw exception if value is null.
     * - Return logger's timezone
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function requireTimezone(): string;

    public function fromArray(array $data): LoggerConfiguratorInterface;

    /**
     * Specification:
     * - Return associative array of plugin's configurators [pluginClassName => configurator]
     *
     * @return \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[]
     */
    public function getPluginConfiguratorCollection(): array;

    /**
     * Specification:
     * - Set associative array of plugin's configurators [pluginClassName => configurator]
     *
     * @param \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[] $pluginConfiguratorCollection
     *
     * @return \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
     */
    public function setPluginConfiguratorCollection(array $pluginConfiguratorCollection): LoggerConfiguratorInterface;

    /**
     * Specification:
     * - Add plugin's configurator to collection
     *
     * @param \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface $pluginConfigurator
     *
     * @return \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
     */
    public function addPluginConfigurator(PluginConfiguratorInterface $pluginConfigurator): LoggerConfiguratorInterface;

    /**
     * Specification:
     * - A plugin class configurator should implement Everon\Logger\Contract\Configurator\PluginConfiguratorInterface
     * - Return configurator by plugin's classname
     *
     * @param string $pluginClass
     *
     * @return \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface
     */
    public function getConfiguratorByPluginName(string $pluginClass): PluginConfiguratorInterface;

    /**
     * Specification:
     * - A processor class should implement Monolog\Processor\ProcessorInterface
     * - Add processor class to logger
     *
     * @param string $item
     *
     * @return \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
     */
    public function addProcessorClass(string $item): LoggerConfiguratorInterface;
}
