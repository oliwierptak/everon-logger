<?php declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Psr\Log\LoggerInterface;

interface EveronLoggerFacadeInterface
{

    /**
     * Specification:
     * - Validate configurator, throw exception on error
     * - Build plugins and resolve their configurators, throw exception on error
     * - Check if plugins can be executed
     * - Run plugins and build monolog handlers, throw exception on error
     * - Build and set formatters when the plugins implement Everon\Logger\Contract\Plugin\PluginFormatterInterface
     * - Build and set processors, specified in the configurator
     * - Configure timezone
     * - Create instance of logger implementing Psr\Log\LoggerInterface
     * - Return logger instance
     *
     * @throws \Everon\Logger\Exception\HandlerBuildException
     * @throws \Everon\Logger\Exception\PluginBuildException
     * @throws \Everon\Logger\Exception\ProcessorBuildException
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    public function buildLogger(LoggerConfiguratorInterface $configurator): LoggerInterface;

}
