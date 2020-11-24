<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Contract\Plugin\PluginFactoryInterface;
use Everon\Logger\Plugin\Stream\StreamLoggerPlugin;

class FactoryStub implements PluginFactoryInterface
{
    public function create(PluginConfiguratorInterface $configurator): LoggerPluginInterface
    {
        /** @var \Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator $configurator */
        return new StreamLoggerPlugin($configurator);
    }
}
