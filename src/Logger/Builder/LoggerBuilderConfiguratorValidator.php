<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Exception\ConfiguratorValidationException;
use InvalidArgumentException;

class LoggerBuilderConfiguratorValidator
{
    /**
     * @param \Everon\Logger\Configurator\LoggerPluginConfigurator $configurator
     *
     * @return void
     *
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    public function validate(LoggerPluginConfigurator $configurator): void
    {
        try {
            $configurator->requireName();
            $configurator->requireTimezone();
        }
        catch (InvalidArgumentException $exception) {
            throw new ConfiguratorValidationException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}
