<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use DateTimeZone;
use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\PluginFormatterInterface;
use Everon\Logger\Exception\HandlerBuildException;
use Everon\Logger\Exception\PluginBuildException;
use Everon\Logger\Exception\ProcessorBuildException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

class LoggerBuilderFromConfigurator
{
    protected LoggerPluginConfigurator $configurator;

    protected PluginBuilder $pluginBuilder;

    protected ConfiguratorValidator $validator;

    public function __construct(
        LoggerPluginConfigurator $configurator,
        PluginBuilder $configuratorResolver,
        ConfiguratorValidator $validator)
    {
        $this->configurator = $configurator;
        $this->pluginBuilder = $configuratorResolver;
        $this->validator = $validator;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     * @throws \Everon\Logger\Exception\HandlerBuildException
     * @throws \Everon\Logger\Exception\PluginBuildException
     * @throws \Everon\Logger\Exception\ProcessorBuildException
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    public function buildLogger(): LoggerInterface
    {
        $this->validator->validate($this->configurator);

        $handlers = $this->buildHandlers();
        $processors = $this->buildProcessors();

        return new Logger(
            $this->configurator->getName(),
            $handlers,
            $processors,
            new DateTimeZone($this->configurator->getTimezone())
        );
    }

    /**
     * @return array
     * @throws \Everon\Logger\Exception\PluginBuildException
     * @throws \Everon\Logger\Exception\HandlerBuildException
     */
    protected function buildHandlers(): array
    {
        $handlers = [];
        foreach ($this->configurator->getPluginClassCollection() as $pluginClass) {
            try {
                $plugin = $this->pluginBuilder->buildPlugin(
                    $this->configurator,
                    $pluginClass
                );

                if (!$plugin->canRun()) {
                    continue;
                }
            }
            catch (Throwable $exception) {
                throw new PluginBuildException(sprintf(
                    'Could not build plugin: "%s". Error: %s',
                    $pluginClass,
                    $exception->getMessage()
                ), $exception->getCode(), $exception);
            }

            try {
                $handler = $plugin->buildHandler();
                if ($plugin instanceof PluginFormatterInterface) {
                    $formatter = $plugin->buildFormatter();
                    $handler->setFormatter($formatter);
                }

                $handlers[] = $handler;
            }
            catch (Throwable $exception) {
                throw new HandlerBuildException(sprintf(
                    'Could not build handler: "%s". Error: %s',
                    $pluginClass,
                    $exception->getMessage()
                ), $exception->getCode(), $exception);
            }
        }

        return $handlers;
    }

    /**
     * @return \Monolog\Processor\ProcessorInterface[]
     * @throws \Everon\Logger\Exception\ProcessorBuildException
     */
    protected function buildProcessors(): array
    {
        $processors = [];
        foreach ($this->configurator->getProcessorClassCollection() as $processorClass) {
            try {
                $processor = new $processorClass();
                $processors[] = $processor;
            }
            catch (Throwable $exception) {
                throw new ProcessorBuildException(sprintf(
                    'Could not build processor: "%s". Error: %s',
                    $processorClass,
                    $exception->getMessage()
                ));
            }
        }

        return $processors;
    }
}
