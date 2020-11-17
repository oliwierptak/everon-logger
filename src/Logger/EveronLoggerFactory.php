<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Builder\HandlerBuilder;
use Everon\Logger\Builder\PluginBuilder;
use Everon\Logger\Builder\ConfiguratorValidator;
use Everon\Logger\Builder\FromConfiguratorBuilder;
use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;

class EveronLoggerFactory
{
    public function createBuilderFromConfigurator(LoggerPluginConfigurator $configurator): FromConfiguratorBuilder
    {
        return new FromConfiguratorBuilder(
            $configurator,
            $this->createPluginBuilder(),
            $this->createHandlerBuilder(),
            $this->createValidator()
        );
    }

    protected function createValidator(): ConfiguratorValidator
    {
        return new ConfiguratorValidator();
    }

    protected function createPluginBuilder(): PluginBuilder
    {
        return new PluginBuilder();
    }

    protected function createHandlerBuilder(): HandlerBuilder
    {
        return new HandlerBuilder();
    }
}
