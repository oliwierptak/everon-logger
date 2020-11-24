<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Syslog;

use Everon\Logger\Configurator\Plugin\SyslogLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

class SyslogLoggerPlugin implements LoggerPluginInterface
{
    protected SyslogLoggerPluginConfigurator $configurator;

    public function __construct(SyslogLoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function canRun(): bool
    {
        return $this->configurator->hasIdent();
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new SyslogHandler(
            $this->configurator->getIdent(),
            $this->configurator->getFacility(),
            Logger::toMonologLevel($this->configurator->requireLogLevel()),
            $this->configurator->shouldBubble(),
            $this->configurator->getLogopts(),
        );
    }

    protected function validate(): void
    {
        $this->configurator->requireIdent();
        $this->configurator->requireFacility();
        $this->configurator->requireLogLevel();
    }
}
