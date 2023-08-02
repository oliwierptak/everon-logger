<?php declare(strict_types = 1);

namespace Everon\Logger\Contract\Plugin;

use Monolog\Handler\HandlerInterface;

interface LoggerPluginInterface
{

    /**
     * Specification:
     * - Check if all the required data is set
     * - Throw exception on error
     *
     * @throws \UnexpectedValueException
     */
    public function validate(): void;

    /**
     * Specification:
     * - Return true if plugin should be executed, false otherwise
     */
    public function canRun(): bool;

    /**
     * Specification:
     * - Create instance of handler implementing Monolog\Handler\HandlerInterface
     * - Set up the log level, based on internal numerical Monolog log level
     * - Return object implementing of Monolog\Handler\HandlerInterface
     */
    public function buildHandler(): HandlerInterface;

}
