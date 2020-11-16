<?php

namespace Everon\Logger\Contract\Plugin;

use Everon\Logger\Configurator\Plugin\GelfLoggerPluginConfigurator;

interface PluginFactoryInterface
{
    public function create(GelfLoggerPluginConfigurator $configurator): LoggerPluginInterface;
}
