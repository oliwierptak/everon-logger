<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Contract\Plugin\PluginFormatterInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class StreamLoggerPluginStub implements LoggerPluginInterface, PluginFormatterInterface
{
    protected StreamLoggerPluginConfiguratorStub $configurator;

    public function __construct(StreamLoggerPluginConfiguratorStub $configurator)
    {
        $this->configurator = $configurator;
    }

    public function canRun(): bool
    {
        return $this->configurator->hasStreamLocation();
    }

    public function buildHandler(): HandlerInterface
    {
        return new StreamHandler(
            $this->configurator->getStreamLocation(),
            Logger::toMonologLevel($this->configurator->requireLogLevel()),
            $this->configurator->shouldBubble(),
            $this->configurator->getFilePermission(),
            $this->configurator->useLocking()
        );
    }

    public function validate(): void
    {
        $this->configurator->requireStreamLocation();
        $this->configurator->requireLogLevel();
    }

    public function buildFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }
}
