<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfHttp;

use Everon\Logger\Plugin\Gelf\AbstractGelfLoggerPlugin;
use Gelf\Transport\AbstractTransport;
use Gelf\Transport\HttpTransport;

class GelfHttpLoggerPlugin extends AbstractGelfLoggerPlugin
{
    public function canRun(): bool
    {
        return $this->configurator->getHttpConfigurator()->hasHost();
    }

    protected function buildTransport(): AbstractTransport
    {
        $sslOptions = $this->buildSslOptions($this->configurator->getHttpConfigurator());

        return new HttpTransport(
            $this->configurator->getHttpConfigurator()->getHost(),
            $this->configurator->getHttpConfigurator()->getPort(),
            $this->configurator->getHttpConfigurator()->getPath(),
            $sslOptions
        );
    }

    protected function validate(): void
    {
        parent::validate();

        $this->configurator->getHttpConfigurator()->requireHost();
        $this->configurator->getHttpConfigurator()->requirePort();
    }

    public function resolveConfigurator()
    {
        return $this->configurator->getHttpConfigurator();
    }
}
