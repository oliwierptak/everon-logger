<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\GelfHttp;

use Everon\Logger\Configurator\Resolver\AbstractLoggerConfiguratorResolver;

class ConfigurationResolver extends AbstractLoggerConfiguratorResolver
{
    public function resolve()
    {
        return $this->configurator->getGelfConfigurator();
    }
}
