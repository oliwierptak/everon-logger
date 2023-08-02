<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use DateTimeZone;
use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Exception\ConfiguratorValidationException;
use Everon\Logger\Exception\ProcessorBuildException;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

class LoggerBuilder
{
    public function __construct(
        protected LoggerConfiguratorInterface $configurator,
        protected PluginBuilder $pluginBuilder,
        protected HandlerBuilder $handlerBuilder,
        protected ConfiguratorValidator $validator
    ) {
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
     * @return array<HandlerInterface>
     * @throws \Exception
     * @throws \Everon\Logger\Exception\PluginBuildException
     * @throws \Everon\Logger\Exception\HandlerBuildException
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    protected function buildHandlers(): array
    {
        $handlers = [];
        foreach ($this->configurator->getPluginConfiguratorCollection() as $pluginClass => $pluginConfigurator) {
            $plugin = $this->pluginBuilder->buildPlugin($pluginConfigurator);

            $this->validateConfiguration($plugin);

            if (!$plugin->canRun()) {
                continue;
            }

            $handlers[] = $this->handlerBuilder->buildHandler($plugin);
        }

        return $handlers;
    }

    /**
     * @param \Everon\Logger\Contract\Plugin\LoggerPluginInterface $plugin
     *
     * @return void
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    protected function validateConfiguration(LoggerPluginInterface $plugin): void
    {
        if ($this->configurator->validateConfiguration()) {
            try {
                $plugin->validate();
            }
            catch (\UnexpectedValueException $exception) {
                throw new ConfiguratorValidationException($exception->getMessage());
            }
        }
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
                /** @var \Monolog\Processor\ProcessorInterface $processor */
                $processor = new $processorClass();
                $processors[] = $processor;
            }
            catch (Throwable $exception) {
                throw new ProcessorBuildException(
                    sprintf(
                        'Could not build processor: "%s". Error: %s',
                        $processorClass,
                        $exception->getMessage()
                    )
                );
            }
        }

        return $processors;
    }
}
