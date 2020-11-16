<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Container;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginStub;
use EveronLoggerTests\Stub\Plugin\Syslog\SyslogLoggerPluginStub;

class PluginContainerStub implements LoggerContainerInterface
{
    protected LoggerPluginConfigurator $configurator;

    public function __construct(LoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function createPluginCollection(): array
    {
        return [
            new StreamLoggerPluginStub($this->configurator->getStreamConfigurator()),
            new SyslogLoggerPluginStub($this->configurator->getSyslogConfigurator()),
        ];
    }

    public function getConfigurator(): LoggerPluginConfigurator
    {
        return $this->configurator;
    }
}
