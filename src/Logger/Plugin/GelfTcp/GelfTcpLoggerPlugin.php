<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfTcp;

use Everon\Logger\Configurator\Plugin\GelfTcpLoggerPluginConfigurator;
use Everon\Logger\Plugin\Gelf\AbstractGelfLoggerPlugin;
use Gelf\Transport\AbstractTransport;
use Gelf\Transport\TcpTransport;

/**
 * @property GelfTcpLoggerPluginConfigurator $configurator
 */
class GelfTcpLoggerPlugin extends AbstractGelfLoggerPlugin
{
    public function canRun(): bool
    {
        return $this->configurator->hasHost();
    }

    protected function buildTransport(): AbstractTransport
    {
        $sslOptions = $this->buildSslOptions($this->configurator);

        return new TcpTransport(
            $this->configurator->getHost(),
            $this->configurator->getPort(),
            $sslOptions
        );
    }

    protected function validate(): void
    {
        parent::validate();

        $this->configurator->requireHost();
        $this->configurator->requirePort();
    }
}
