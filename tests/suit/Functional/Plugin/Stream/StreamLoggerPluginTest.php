<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Stream;

use Everon\Logger\Contract\Container\LoggerContainerInterface;
use EveronLoggerTests\Stub\Container\PluginContainerStub;
use EveronLoggerTests\Stub\Container\PluginContainerWithProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;

class StreamLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected LoggerContainerInterface $pluginContainer;

    public function test_should_not_log_without_logFile(): void
    {
        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log(): void
    {
        $this->configurator->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertLoggerFile('foo bar', 'info', [], [], 0);
        $this->assertLoggerFile('foo bar warning', 'warning', [], [], 1);
    }

    public function test_should_log_context(): void
    {
        $this->configurator->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->pluginContainer = new PluginContainerWithProcessorStub($this->configurator);

        $this->configurator->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLoggerFromContainer($this->pluginContainer);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->pluginContainer = new PluginContainerStub($this->configurator);
    }
}
