<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Stream;

use Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class StreamLoggerPlugin implements LoggerPluginInterface
{
    protected StreamLoggerPluginConfigurator $configurator;

    public function __construct(StreamLoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function canRun(): bool
    {
        return $this->configurator->hasStreamLocation();
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new StreamHandler(
            $this->configurator->getStreamLocation(),
            Logger::toMonologLevel($this->configurator->requireLogLevel()),
            $this->configurator->shouldBubble(),
            $this->configurator->getFilePermission(),
            $this->configurator->useLocking()
        );
    }

    protected function validate(): void
    {
        $this->configurator->requireStreamLocation();
        $this->configurator->requireLogLevel();
    }
}
