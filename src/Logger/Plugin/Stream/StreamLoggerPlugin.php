<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Stream;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class StreamLoggerPlugin implements LoggerPluginInterface
{
    protected LoggerPluginConfigurator $configurator;

    public function __construct(LoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new StreamHandler(
            $this->configurator->getStreamConfigurator()->getStreamLocation(),
            Logger::toMonologLevel($this->configurator->getLogLevel()),
            $this->configurator->getStreamConfigurator()->shouldBubble(),
            $this->configurator->getStreamConfigurator()->getFilePermission(),
            $this->configurator->getStreamConfigurator()->useLocking()
        );
    }

    public function canRun(): bool
    {
        return $this->configurator->getStreamConfigurator()->hasStreamLocation();
    }

    protected function validate(): void
    {
        $this->configurator->getStreamConfigurator()->requireStreamLocation();
        $this->configurator->requireLogLevel();
    }
}
