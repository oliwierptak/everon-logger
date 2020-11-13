<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Builder\LoggerBuilder;
use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;

class EveronLoggerFactory
{
    public function createBuilder(
        LoggerPluginConfigurator $configurator,
        LoggerContainerInterface $pluginContainer): LoggerBuilder
    {
        return new LoggerBuilder(
            $configurator,
            $pluginContainer
        );
    }
}
