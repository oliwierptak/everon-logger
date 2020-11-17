<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Configurator\AbstractPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Contract\Plugin\PluginFactoryInterface;

class FactoryStub implements PluginFactoryInterface
{
    public function create(AbstractPluginConfigurator $configurator): LoggerPluginInterface
    {
        /** @var \Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator $configurator */
        return new StreamLoggerPluginStub($configurator);
    }
}
