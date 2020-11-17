<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfHttp;

use Everon\Logger\Configurator\Plugin\GelfHttpLoggerPluginConfigurator;
use Everon\Logger\Plugin\Gelf\AbstractGelfLoggerPlugin;
use Gelf\Transport\AbstractTransport;
use Gelf\Transport\HttpTransport;

/**
 * @property GelfHttpLoggerPluginConfigurator $configurator
 */
class GelfHttpLoggerPlugin extends AbstractGelfLoggerPlugin
{
    public function canRun(): bool
    {
        return $this->configurator->hasHost();
    }

    protected function buildTransport(): AbstractTransport
    {
        $sslOptions = $this->buildSslOptions($this->configurator);

        return new HttpTransport(
            $this->configurator->getHost(),
            $this->configurator->getPort(),
            $this->configurator->getPath(),
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
