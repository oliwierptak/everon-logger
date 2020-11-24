<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\ErrorLog;

use Everon\Logger\Configurator\Plugin\ErrorLogLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class ErrorLogLoggerPlugin implements LoggerPluginInterface
{
    protected ErrorLogLoggerPluginConfigurator $configurator;

    public function __construct(ErrorLogLoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function canRun(): bool
    {
        return $this->configurator->hasMessageType();
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new ErrorLogHandler(
            $this->configurator->getMessageType(),
            Logger::toMonologLevel($this->configurator->requireLogLevel()),
            $this->configurator->shouldBubble(),
            $this->configurator->expandNewlines()
        );
    }

    protected function validate(): void
    {
        $this->configurator->requireMessageType();
        $this->configurator->requireLogLevel();
    }
}
