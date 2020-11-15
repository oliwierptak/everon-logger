<?php

declare(strict_types = 1);

namespace Everon\Logger\Contract\Container;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;

interface LoggerContainerInterface
{
    /**
     * Specification:
     * - Create set of plugins implementing \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface[]
     */
    public function createPluginCollection(): array;

    /**
     * Specification:
     * - Return instance of LoggerPluginConfigurator
     *
     * @return \Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator
     */
    public function getConfigurator(): LoggerPluginConfigurator;
}
