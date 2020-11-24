<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Stream;

use Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator;
use Everon\Logger\Plugin\Stream\StreamLoggerPlugin;
use EveronLoggerTests\Stub\Plugin\Stream\FactoryStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Configurator\TestLoggerConfigurator;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;

class StreamLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator())
            ->setPluginClass(StreamLoggerPlugin::class)
            ->setLogLevel('debug');

        $this->configurator->addPluginConfigurator($streamPluginConfigurator);
    }

    public function test_should_not_log_without_logFile(): void
    {
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(StreamLoggerPlugin::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(StreamLoggerPlugin::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info'));

        $logger->warning('foo bar warning');
        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar warning')
            ->setLevel('warning'));
    }

    public function test_should_log_context(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(StreamLoggerPlugin::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum']));
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator
            ->addProcessorClass(MemoryUsageProcessorStub::class)
            ->getConfiguratorByPluginName(StreamLoggerPlugin::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum'])
            ->setExtra(['memory_peak_usage' => '5 MB']));
    }

    public function test_should_use_plugin_factory(): void
    {
        $this->configurator
            ->addProcessorClass(MemoryUsageProcessorStub::class)
            ->getConfiguratorByPluginName(StreamLoggerPlugin::class)
            ->setPluginFactoryClass(FactoryStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum'])
            ->setExtra(['memory_peak_usage' => '5 MB']));
    }
}
