<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Gelf;

use Everon\Logger\Configurator\Plugin\GelfLoggerPluginSslOptions;

abstract class AbstractGelfSslPluginConfigurator extends AbstractGelfPluginConfigurator
{
    abstract public function getSslOptions(): ?GelfLoggerPluginSslOptions;
}
