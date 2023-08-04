<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Contract\Plugin\PluginFactoryInterface;

class FactoryStub implements PluginFactoryInterface
{
    public function create(PluginConfiguratorInterface $configurator): LoggerPluginInterface
    {
        return new HandlerExceptionLoggerPluginStub($configurator);
    }
}
