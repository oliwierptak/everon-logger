<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use InvalidArgumentException;
use Monolog\Handler\HandlerInterface;

class PluginExceptionLoggerPluginStub implements LoggerPluginInterface
{
    protected StreamLoggerPluginConfiguratorStub $configurator;

    public function __construct(StreamLoggerPluginConfiguratorStub $configurator)
    {
        throw new InvalidArgumentException('Invalid value for foo bar');
    }

    public function canRun(): bool
    {
        return true;
    }

    public function buildHandler(): HandlerInterface {}

    public function validate(): void
    {
        throw new InvalidArgumentException('Invalid value for foo bar');
    }
}
