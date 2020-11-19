<?php

namespace Everon\Logger\Contract\Plugin;

use Everon\Logger\Configurator\AbstractPluginConfigurator;

interface PluginFactoryInterface
{
    /**
     * Specification:
     * - Create plugin using custom logic
     * - Return logger plugin instance
     *
     * @param \Everon\Logger\Configurator\AbstractPluginConfigurator $configurator
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     */
    public function create(AbstractPluginConfigurator $configurator): LoggerPluginInterface;
}
