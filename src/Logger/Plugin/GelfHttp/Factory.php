<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfHttp;

use Everon\Logger\Configurator\Plugin\GelfLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Contract\Plugin\PluginFactoryInterface;

class Factory implements PluginFactoryInterface
{
    public function create(GelfLoggerPluginConfigurator $configurator): LoggerPluginInterface
    {
        return new GelfHttpLoggerPlugin($configurator);
    }
}
