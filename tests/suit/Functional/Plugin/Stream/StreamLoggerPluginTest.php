<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Stream;

use Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator;
use EveronLoggerTests\Stub\Plugin\Stream\FactoryStub;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;

class StreamLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator())
            ->setPluginClass(StreamLoggerPluginStub::class)
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
        $pluginConfigurator = $this->configurator->getPluginConfiguratorByPluginName(StreamLoggerPluginStub::class);
        $pluginConfigurator
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log(): void
    {
        $pluginConfigurator = $this->configurator->getPluginConfiguratorByPluginName(StreamLoggerPluginStub::class);
        $pluginConfigurator
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertLoggerFile('foo bar', 'info', [], [], 0);
        $this->assertLoggerFile('foo bar warning', 'warning', [], [], 1);
    }

    public function test_should_log_context(): void
    {
        $pluginConfigurator = $this->configurator->getPluginConfiguratorByPluginName(StreamLoggerPluginStub::class);
        $pluginConfigurator
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator->addProcessorClass(MemoryUsageProcessorStub::class);

        $pluginConfigurator = $this->configurator->getPluginConfiguratorByPluginName(StreamLoggerPluginStub::class);
        $pluginConfigurator
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }

    public function test_should_use_plugin_factory(): void
    {
        $this->configurator->addProcessorClass(MemoryUsageProcessorStub::class);

        $pluginConfigurator = $this->configurator->getPluginConfiguratorByPluginName(StreamLoggerPluginStub::class);
        $pluginConfigurator
            ->setPluginFactoryClass(FactoryStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }

    protected function assertLoggerFile(
        string $message,
        string $level,
        array $context = [],
        array $extra = [],
        int $index = 0
    ): void
    {
        $jsonContextString = json_encode($context);
        $jsonExtraString = json_encode($extra);

        $expected = sprintf(
            '%s: %s %s %s' . \PHP_EOL,
            \strtoupper($level),
            $message,
            $jsonContextString,
            $jsonExtraString
        );

        $this->assertFileExists($this->logFilename);
        $this->assertEquals($expected, file($this->logFilename)[$index]);
    }
}
