<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Gelf;

use Everon\Logger\Configurator\Plugin\GelfLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Gelf\Publisher;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\TransportInterface;
use Monolog\Handler\GelfHandler;
use Monolog\Handler\HandlerInterface;

abstract class AbstractGelfLoggerPlugin implements LoggerPluginInterface
{
    protected GelfLoggerPluginConfigurator $configurator;

    /**
     * @return \Gelf\Transport\AbstractTransport|\Gelf\Transport\TransportInterface
     */
    abstract protected function buildTransport(): TransportInterface;

    abstract public function canRun(): bool;

    public function __construct(GelfLoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        $transport = new IgnoreErrorTransportWrapper($this->buildTransport());
        $publisher = new Publisher($transport);

        return new GelfHandler($publisher, $this->configurator->getLogLevel(), $this->configurator->shouldBubble());
    }

    protected function validate(): void
    {
        $this->configurator->requireLogLevel();
    }
}
