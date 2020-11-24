<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use DateTimeZone;
use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\Exception\ProcessorBuildException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

class LoggerBuilder
{
    protected LoggerConfiguratorInterface $configurator;

    protected PluginBuilder $pluginBuilder;

    protected HandlerBuilder $handlerBuilder;

    protected ConfiguratorValidator $validator;

    public function __construct(
        LoggerConfiguratorInterface $configurator,
        PluginBuilder $pluginBuilder,
        HandlerBuilder $handlerBuilder,
        ConfiguratorValidator $validator)
    {
        $this->configurator = $configurator;
        $this->pluginBuilder = $pluginBuilder;
        $this->handlerBuilder = $handlerBuilder;
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
            $this->configurator->requireName(),
            $handlers,
            $processors,
            new DateTimeZone($this->configurator->requireTimezone())
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
        foreach ($this->configurator->getPluginConfiguratorCollection() as $pluginClass => $pluginConfigurator) {
            $plugin = $this->pluginBuilder->buildPlugin($pluginConfigurator);

            if (!$plugin->canRun()) {
                continue;
            }

            $handlers[] = $this->handlerBuilder->buildHandler($plugin);
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
