<?php

declare(strict_types = 1);

namespace Everon\Logger\Contract\Container;

interface LoggerConfiguratorContainerInterface
{
    /**
     * Specification:
     * - Create set of plugins implementing \Everon\Logger\Configurator\AutoConfiguratorPluginInterface
     *
     * @return \Everon\Logger\Configurator\AutoConfiguratorPluginInterface[]
     */
    public function createConfiguratorPluginCollection(): array;
}
