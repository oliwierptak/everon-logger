<?php

namespace Everon\Logger\Contract\Plugin;

use Everon\Logger\Configurator\AbstractPluginConfigurator;

interface PluginFactoryInterface
{
    public function create(AbstractPluginConfigurator $configurator): LoggerPluginInterface;
}
