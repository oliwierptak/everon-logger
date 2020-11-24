<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Builder\HandlerBuilder;
use Everon\Logger\Builder\PluginBuilder;
use Everon\Logger\Builder\ConfiguratorValidator;
use Everon\Logger\Builder\LoggerBuilder;
use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;

class EveronLoggerFactory
{
    public function createBuilderFromConfigurator(LoggerConfiguratorInterface $configurator): LoggerBuilder
    {
        return new LoggerBuilder(
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
