<?php

namespace Everon\Logger;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Psr\Log\LoggerInterface;

interface EveronLoggerFacadeInterface
{
    /**
     * Specification:
     * - Validate configurator, throw exception on error
     * - Build plugins and resolve their configurators, throw exception on error
     * - Check if plugins can be executed
     * - Run plugins and build monolog handlers
     * - Build and set formatters when the plugins implement Everon\Logger\Contract\Plugin\PluginFormatterInterface
     * - Build and set processors when the container implements Everon\Logger\Contract\Container\Plugin\LoggerProcessorContainerInterface
     * - Configure timezone
     * - Create instance of logger implementing Psr\Log\LoggerInterface
     * - Return logger instance
     *
     * @param \Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator $configurator
     *
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \Everon\Logger\Exception\ProcessorBuildException
     * @throws \Everon\Logger\Exception\HandlerBuildException
     * @throws \Everon\Logger\Exception\PluginBuildException
     */
    public function buildLogger(LoggerPluginConfigurator $configurator): LoggerInterface;

    /**
     * Specification:
     * - Validate configurator, throw exception on error
     * - Check if plugins can be executed
     * - Run plugins and build monolog handlers
     * - Build and set formatters when the plugins implement Everon\Logger\Contract\Plugin\PluginFormatterInterface
     * - Build and set processors when the container implements Everon\Logger\Contract\Container\Plugin\LoggerProcessorContainerInterface
     * - Configure timezone
     * - Create instance of logger implementing Psr\Log\LoggerInterface
     * - Return logger instance
     *
     * @param \Everon\Logger\Contract\Container\LoggerContainerInterface $pluginContainer
     *
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \Everon\Logger\Exception\PluginBuildException
     * @throws \Everon\Logger\Exception\ProcessorBuildException
     * @throws \Everon\Logger\Exception\HandlerBuildException
     */
    public function buildLoggerFromContainer(LoggerContainerInterface $pluginContainer): LoggerInterface;
}
