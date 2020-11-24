<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Nullee;

use Everon\Logger\Configurator\Plugin\NulleeLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;

class NulleeLoggerPlugin implements LoggerPluginInterface
{
    protected NulleeLoggerPluginConfigurator $configurator;

    public function __construct(NulleeLoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function canRun(): bool
    {
        return $this->configurator->hasLogLevel();
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new NullHandler($this->configurator->requireLogLevel());
    }

    protected function validate(): void
    {
        $this->configurator->requireLogLevel();
    }
}
