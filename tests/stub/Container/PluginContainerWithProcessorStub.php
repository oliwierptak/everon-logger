<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Container;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Everon\Logger\Contract\Container\LoggerProcessorContainerInterface;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginFormatterStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;

class PluginContainerWithProcessorStub implements LoggerContainerInterface, LoggerProcessorContainerInterface
{
    protected LoggerPluginConfigurator $configurator;

    public function __construct(LoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function createPluginCollection(): array
    {
        return [
            new StreamLoggerPluginFormatterStub($this->configurator->getStreamConfigurator()),
        ];
    }

    public function createProcessorCollection(): array
    {
        return [
            new MemoryUsageProcessorStub(),
        ];
    }

    public function getConfigurator(): LoggerPluginConfigurator
    {
        return $this->configurator;
    }
}
