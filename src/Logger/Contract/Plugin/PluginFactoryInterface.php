<?php

namespace Everon\Logger\Contract\Plugin;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;

interface PluginFactoryInterface
{
    /**
     * Specification:
     * - Create plugin using custom logic or external dependencies
     * - Return logger plugin instance
     *
     * @param \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface $configurator
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     */
    public function create(PluginConfiguratorInterface $configurator): LoggerPluginInterface;
}
