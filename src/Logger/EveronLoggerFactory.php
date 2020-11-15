<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Builder\LoggerBuilderConfiguratorValidator;
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
            $this->createLoggerBuilderConfiguratorValidator()
        );
    }

    protected function createLoggerBuilderConfiguratorValidator(): LoggerBuilderConfiguratorValidator
    {
        return new LoggerBuilderConfiguratorValidator();
    }

    public function createBuilderFromContainer(LoggerContainerInterface $pluginContainer): LoggerBuilderFromContainer
    {
        return new LoggerBuilderFromContainer(
            $pluginContainer,
            $this->createLoggerBuilderConfiguratorValidator()
        );
    }
}
