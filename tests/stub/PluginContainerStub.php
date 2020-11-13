<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerConfiguratorContainerInterface;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Everon\Logger\Contract\Container\LoggerProcessorContainerInterface;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginStub;
use EveronLoggerTests\Stub\Plugin\Syslog\SyslogLoggerPluginStub;

class PluginContainerStub implements
    LoggerContainerInterface, LoggerProcessorContainerInterface, LoggerConfiguratorContainerInterface
{
    protected LoggerPluginConfigurator $configurator;

    public function __construct(LoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function createPluginCollection(): array
    {
        return [
            new StreamLoggerPluginStub($this->configurator),
            new SyslogLoggerPluginStub($this->configurator),
        ];
    }

    public function createConfiguratorPluginCollection(): array
    {
        return [];
    }

    public function createProcessorCollection(): array
    {
        return [];
    }
}
