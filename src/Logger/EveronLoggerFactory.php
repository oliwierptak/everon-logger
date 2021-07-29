<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Builder\HandlerBuilder;
use Everon\Logger\Builder\PluginBuilder;
use Everon\Logger\Builder\ConfiguratorValidator;
use Everon\Logger\Builder\LoggerBuilder;
use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use JetBrains\PhpStorm\Pure;

class EveronLoggerFactory
{
    #[Pure] public function createBuilderFromConfigurator(LoggerConfiguratorInterface $configurator): LoggerBuilder
    {
        return new LoggerBuilder(
            $configurator,
            $this->createPluginBuilder(),
            $this->createHandlerBuilder(),
            $this->createValidator()
        );
    }

    #[Pure] protected function createValidator(): ConfiguratorValidator
    {
        return new ConfiguratorValidator();
    }

    #[Pure] protected function createPluginBuilder(): PluginBuilder
    {
        return new PluginBuilder();
    }

    #[Pure] protected function createHandlerBuilder(): HandlerBuilder
    {
        return new HandlerBuilder();
    }
}
