<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator\Resolver;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;

abstract class AbstractLoggerConfiguratorResolver
{
    protected LoggerPluginConfigurator $configurator;

    abstract public function resolve();

    public function __construct(LoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }
}
