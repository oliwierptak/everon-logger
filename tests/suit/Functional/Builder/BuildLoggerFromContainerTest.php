<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Builder;

use Everon\Logger\Contract\Container\LoggerContainerInterface;
use EveronLoggerTests\Stub\Container\PluginContainerWithProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;
use Psr\Log\LoggerInterface;

class BuildLoggerFromContainerTest extends AbstractPluginLoggerTest
{
    protected LoggerContainerInterface $pluginContainer;

    protected function setUp(): void
    {
        parent::setUp();;

        $this->pluginContainer = new PluginContainerWithProcessorStub($this->configurator);
    }

    public function test_build_logger(): void
    {
        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

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

    public function test_should_log_extra(): void
    {
        $this->configurator->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertLoggerFile('foo bar', 'info', [], ['memory_peak_usage' => '5 MB'], 0);
        $this->assertLoggerFile('foo bar warning', 'warning', [], ['memory_peak_usage' => '5 MB'], 1);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }
}
