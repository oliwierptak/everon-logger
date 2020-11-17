<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Gelf;

use Everon\Logger\Configurator\AbstractPluginConfigurator;

abstract class AbstractGelfPluginConfigurator extends AbstractPluginConfigurator
{
    abstract public function requireLogLevel(): string;

    abstract public function getLogLevel(): ?string;

    abstract public function shouldBubble(): ?bool;

    abstract public function ignoreTransportErrors(): ?bool;
}
