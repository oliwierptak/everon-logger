<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Builder\PluginBuilder;
use Everon\Logger\Builder\ConfiguratorValidator;
use Everon\Logger\Builder\LoggerBuilderFromConfigurator;
use Everon\Logger\Builder\LoggerBuilderFromContainer;
use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;

class EveronLoggerFactory
{
    public function createBuilderFromConfigurator(LoggerPluginConfigurator $configurator): LoggerBuilderFromConfigurator
    {
        return new LoggerBuilderFromConfigurator(
            $configurator,
            $this->createPluginBuilder(),
            $this->createValidator()
        );
    }

    public function createBuilderFromContainer(LoggerContainerInterface $pluginContainer): LoggerBuilderFromContainer
    {
        return new LoggerBuilderFromContainer(
            $pluginContainer,
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
}
