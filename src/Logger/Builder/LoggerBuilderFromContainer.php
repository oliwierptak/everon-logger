<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use DateTimeZone;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Everon\Logger\Contract\Container\LoggerProcessorContainerInterface;
use Everon\Logger\Contract\Plugin\PluginFormatterInterface;
use Everon\Logger\Exception\HandlerBuildException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;
use function get_class;

class LoggerBuilderFromContainer
{
    protected LoggerContainerInterface $pluginContainer;

    protected ConfiguratorValidator $validator;

    public function __construct(
        LoggerContainerInterface $pluginContainer,
        ConfiguratorValidator $validator)
    {
        $this->pluginContainer = $pluginContainer;
        $this->validator = $validator;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     * @throws \Everon\Logger\Exception\HandlerBuildException
     * @throws \Everon\Logger\Exception\ConfiguratorValidationException
     */
    public function buildLogger(): LoggerInterface
    {
        $this->validator->validate($this->pluginContainer->getConfigurator());

        $handlers = $this->buildHandlers();
        $processors = $this->buildProcessors();

        return new Logger(
            $this->pluginContainer->getConfigurator()->getName(),
            $handlers,
            $processors,
            new DateTimeZone($this->pluginContainer->getConfigurator()->getTimezone())
        );
    }

    /**
     * @return \Monolog\Handler\HandlerInterface[]
     * @throws \Everon\Logger\Exception\HandlerBuildException
     */
    protected function buildHandlers(): array
    {
        $handlers = [];
        foreach ($this->pluginContainer->createPluginCollection() as $plugin) {
            if (!$plugin->canRun()) {
                continue;
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
                    get_class($plugin),
                    $exception->getMessage()
                ), $exception->getCode(), $exception);
            }
        }

        return $handlers;
    }

    /**
     * @return \Monolog\Processor\ProcessorInterface[]
     */
    protected function buildProcessors(): array
    {
        $processors = [];

        if ($this->pluginContainer instanceof LoggerProcessorContainerInterface) {
            $processors = $this->pluginContainer->createProcessorCollection();
        }

        return $processors;
    }
}
