<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator;

interface AutoConfiguratorPluginInterface
{
    /**
     * Specification:
     * - Update properties of LoggerConfigurator with proper configuration values from an external source
     * - Return updated LoggerConfigurator
     *
     * @param \Everon\Logger\Configurator\LoggerPluginConfigurator $configurator
     *
     * @return \Everon\Logger\Configurator\LoggerPluginConfigurator
     */
    public function autoConfigure(LoggerPluginConfigurator $configurator): LoggerPluginConfigurator;
}
