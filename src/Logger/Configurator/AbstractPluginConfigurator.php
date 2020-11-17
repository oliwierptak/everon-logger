<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator;

abstract class AbstractPluginConfigurator extends AbstractConfigurator
{
    abstract public function getPluginClass(): ?string;

    abstract public function getPluginFactoryClass(): ?string;
}
