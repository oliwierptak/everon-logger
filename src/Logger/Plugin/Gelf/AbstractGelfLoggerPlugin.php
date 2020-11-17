<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Gelf;

use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Gelf\Publisher;
use Gelf\PublisherInterface;
use Gelf\Transport\AbstractTransport;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\SslOptions;
use Monolog\Handler\GelfHandler;
use Monolog\Handler\HandlerInterface;

abstract class AbstractGelfLoggerPlugin implements LoggerPluginInterface
{
    protected AbstractGelfPluginConfigurator $configurator;

    abstract protected function buildTransport(): AbstractTransport;

    public function __construct(AbstractGelfPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();
        $publisher = $this->buildPublisher();

        return new GelfHandler(
            $publisher,
            $this->configurator->getLogLevel(),
            $this->configurator->shouldBubble()
        );
    }

    protected function buildPublisher(): PublisherInterface
    {
        $transport = $this->buildTransport();
        if ($this->configurator->ignoreTransportErrors()) {
            $transport = new IgnoreErrorTransportWrapper($transport);
        }

        return new Publisher($transport);
    }

    protected function validate(): void
    {
        $this->configurator->requireLogLevel();
    }

    protected function buildSslOptions(AbstractGelfSslPluginConfigurator $pluginConfigurator): ?SslOptions
    {
        if (!$pluginConfigurator->getSslOptions()->useSsl()) {
            return null;
        }

        $sslOptions = new SslOptions();

        $sslOptions->setVerifyPeer(
            $pluginConfigurator->getSslOptions()->verifyPeer()
        );
        $sslOptions->setAllowSelfSigned(
            $pluginConfigurator->getSslOptions()->allowSelfSigned()
        );
        $sslOptions->setCaFile(
            $pluginConfigurator->getSslOptions()->getCaFile()
        );
        $sslOptions->setCiphers(
            $pluginConfigurator->getSslOptions()->getCiphers()
        );

        return $sslOptions;
    }
}
