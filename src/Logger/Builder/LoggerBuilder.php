<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use DateTimeZone;
use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Everon\Logger\Contract\Container\LoggerProcessorContainerInterface;
use Everon\Logger\Contract\Plugin\LoggerFormatterPluginInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use function get_class;

class LoggerBuilder
{
    protected LoggerPluginConfigurator $configurator;

    protected LoggerContainerInterface $pluginContainer;

    public function __construct(LoggerPluginConfigurator $configurator, LoggerContainerInterface $pluginContainer)
    {
        $this->configurator = $configurator;
        $this->pluginContainer = $pluginContainer;
    }

    public function buildLogger(): LoggerInterface
    {
        $this->validate();

        $handlers = $this->buildHandlers();
        $processors = $this->buildProcessors();

        return new Logger(
            $this->configurator->getName(),
            $handlers,
            $processors,
            new DateTimeZone($this->configurator->getTimezone())
        );
    }

    protected function buildHandlers(): array
    {
        $handlers = [];
        foreach ($this->pluginContainer->createPluginCollection() as $plugin) {
            if (!$plugin->canRun()) {
                continue;
            }

            $handler = $plugin->buildHandler();
            if ($plugin instanceof LoggerFormatterPluginInterface) {
                $formatter = $plugin->buildFormatter();
                $handler->setFormatter($formatter);
            }

            $handlers[get_class($plugin)] = $handler;
        }

        return $handlers;
    }

    protected function validate(): void
    {
        $this->configurator->requireName();
        $this->configurator->requireTimezone();
    }

    protected function buildProcessors(): array
    {
        $processors = [];

        if ($this->pluginContainer instanceof LoggerProcessorContainerInterface) {
            $processors = $this->pluginContainer->createProcessorCollection();
        }

        return $processors;
    }
}
