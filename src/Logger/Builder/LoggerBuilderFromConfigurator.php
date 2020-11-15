<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use DateTimeZone;
use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginFormatterInterface;
use Everon\Logger\Exception\HandlerBuildException;
use Everon\Logger\Exception\PluginBuildException;
use Everon\Logger\Exception\ProcessorBuildException;
use LogicException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;
use function ucfirst;

class LoggerBuilderFromConfigurator
{
    protected LoggerPluginConfigurator $configurator;

    protected LoggerBuilderConfiguratorValidator $validator;

    public function __construct(
        LoggerPluginConfigurator $configurator,
        LoggerBuilderConfiguratorValidator $validator)
    {
        $this->configurator = $configurator;
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
            $pluginConfigurator = $this->resolvePluginConfigurator($pluginClass);
            try {
                $plugin = new $pluginClass($pluginConfigurator);
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
                if ($plugin instanceof LoggerPluginFormatterInterface) {
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
     * Returns plugin configurator found in LoggerPluginConfigurator, or throws exception
     *
     * @param string $pluginClass
     *
     * @return mixed
     * @throws \LogicException
     */
    protected function resolvePluginConfigurator(string $pluginClass)
    {
        try {
            $tokens = explode('\\', $pluginClass);
            array_pop($tokens);

            $configuratorResolverClass = implode('\\', $tokens) . 'ConfigurationResolver';
            if (\class_exists($configuratorResolverClass)) {
                return (new $configuratorResolverClass)->resolve();
            }

            $namespaceTokens = explode('\\', $pluginClass);
            $pluginName = array_pop($namespaceTokens);

            $tokens = preg_split('/(?<=[^A-Z])(?=[A-Z])/', $pluginName);
            $pluginName = array_shift($tokens);

            $getter = sprintf('get%sConfigurator', ucfirst($pluginName));
            $pluginConfigurator = $this->configurator->$getter();

            return $pluginConfigurator;
        }
        catch (Throwable $exception) {
            throw new LogicException(sprintf(
                'Could not resolve configurator for plugin class: "%s". Error: "%s"',
                $pluginClass,
                $exception->getMessage()
            ));
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
