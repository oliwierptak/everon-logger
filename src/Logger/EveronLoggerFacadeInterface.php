<?php

namespace Everon\Logger;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Psr\Log\LoggerInterface;

interface EveronLoggerFacadeInterface
{
    /**
     * Specification:
     * - Validate configurator, throw exception on error
     * - Check if plugins can be executed
     * - Run plugins and Build monolog handlers
     * - Build and set formatters when the plugins implement Everon\Logger\Contract\Plugin\LoggerFormatterPluginInterface
     * - Build and set processors when the container implements Everon\Logger\Contract\Container\Plugin\LoggerProcessorContainerInterface
     * - Configure timezone
     * - Create instance of logger implementing Psr\Log\LoggerInterface
     * - Return logger instance
     *
     * @param \Everon\Logger\Configurator\LoggerPluginConfigurator $configurator
     * @param \Everon\Logger\Contract\Container\LoggerContainerInterface $pluginContainer
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function buildLogger(
        LoggerPluginConfigurator $configurator,
        LoggerContainerInterface $pluginContainer): LoggerInterface;
}
