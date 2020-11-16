<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfTcp;

use Everon\Logger\Plugin\Gelf\AbstractGelfLoggerPlugin;
use Gelf\Transport\AbstractTransport;
use Gelf\Transport\TcpTransport;

class GelfTcpLoggerPlugin extends AbstractGelfLoggerPlugin
{
    public function canRun(): bool
    {
        return $this->configurator->getTcpConfigurator()->hasHost();
    }

    protected function buildTransport(): AbstractTransport
    {
        $sslOptions = $this->buildSslOptions($this->configurator->getTcpConfigurator());

        return new TcpTransport(
            $this->configurator->getTcpConfigurator()->getHost(),
            $this->configurator->getTcpConfigurator()->getPort(),
            $sslOptions
        );
    }

    protected function validate(): void
    {
        parent::validate();

        $this->configurator->getTcpConfigurator()->requireHost();
        $this->configurator->getTcpConfigurator()->requirePort();
    }

    public function resolveConfigurator()
    {
        return $this->configurator->getTcpConfigurator();
    }
}
