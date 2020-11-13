<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Syslog;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

class SyslogLoggerPlugin implements LoggerPluginInterface
{
    protected LoggerPluginConfigurator $configurator;

    public function __construct(LoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new SyslogHandler(
            $this->configurator->getSyslogConfigurator()->getIdent(),
            $this->configurator->getSyslogConfigurator()->getFacility(),
            Logger::toMonologLevel($this->configurator->getLogLevel()),
            $this->configurator->getSyslogConfigurator()->shouldBubble(),
            $this->configurator->getSyslogConfigurator()->getLogopts(),
        );
    }

    public function canRun(): bool
    {
        return $this->configurator->getSyslogConfigurator()->hasIdent();
    }

    protected function validate(): void
    {
        $this->configurator->getSyslogConfigurator()->requireIdent();
        $this->configurator->getSyslogConfigurator()->requireFacility();
        $this->configurator->requireLogLevel();
    }
}
