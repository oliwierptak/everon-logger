<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator;

abstract class AbstractPluginConfigurator extends AbstractConfigurator
{
    abstract public function getPluginClass(): ?string;

    abstract public function getPluginFactoryClass(): ?string;

    abstract public function requireLogLevel(): string;

    abstract public function getLogLevel(): ?string;

    abstract public function shouldBubble(): ?bool;
}
