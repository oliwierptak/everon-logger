<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\Exception\ConfiguratorValidationException;
use UnexpectedValueException;

class ConfiguratorValidator
{
    /**
     *
     * @param \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface $configurator
     *
     * @return void
     *
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    public function validate(LoggerConfiguratorInterface $configurator): void
    {
        try {
            $configurator->requireName();
            $configurator->requireTimezone();
        }
        catch (UnexpectedValueException $exception) {
            throw new ConfiguratorValidationException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}
