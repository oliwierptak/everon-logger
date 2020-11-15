<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfHttp;

use Everon\Logger\Plugin\Gelf\AbstractGelfLoggerPlugin;
use Gelf\Transport\HttpTransport;
use Gelf\Transport\TransportInterface;

class GelfHttpLoggerPlugin extends AbstractGelfLoggerPlugin
{
    protected function buildTransport(): TransportInterface
    {
        return new HttpTransport(
            $this->configurator->getHttpConfigurator()->getHost(),
            $this->configurator->getHttpConfigurator()->getPort(),
            $this->configurator->getHttpConfigurator()->getSslOptions()
        );
    }

    public function canRun(): bool
    {
        return $this->configurator->getHttpConfigurator()->hasHost();
    }

    protected function validate(): void
    {
        parent::validate();

        $this->configurator->getHttpConfigurator()->requireHost();
        $this->configurator->getHttpConfigurator()->requirePort();
    }
}
