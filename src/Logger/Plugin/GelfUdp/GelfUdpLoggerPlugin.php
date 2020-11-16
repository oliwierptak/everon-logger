<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfUdp;

use Everon\Logger\Plugin\Gelf\AbstractGelfLoggerPlugin;
use Gelf\Transport\AbstractTransport;
use Gelf\Transport\UdpTransport;

class GelfUdpLoggerPlugin extends AbstractGelfLoggerPlugin
{
    public function canRun(): bool
    {
        return $this->configurator->getUdpConfigurator()->hasHost();
    }

    protected function buildTransport(): AbstractTransport
    {
        return new UdpTransport(
            $this->configurator->getUdpConfigurator()->getHost(),
            $this->configurator->getUdpConfigurator()->getPort(),
            $this->configurator->getUdpConfigurator()->getChunkSize()
        );
    }

    protected function validate(): void
    {
        parent::validate();

        $this->configurator->getUdpConfigurator()->requireHost();
        $this->configurator->getUdpConfigurator()->requirePort();
        $this->configurator->getUdpConfigurator()->requireChunkSize();
    }

    public function resolveConfigurator()
    {
        return $this->configurator->getUdpConfigurator();
    }
}
