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
     * - Build monolog handlers
     * - Build and set formatter when the plugin implements Everon\Logger\Contract\Plugin\LoggerFormatterPluginInterface
     * - Build processors when the container implements Everon\Logger\Contract\Container\Plugin\LoggerProcessorContainerInterface
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
