<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Builder;

use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginFormatterStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;
use Psr\Log\LoggerInterface;

class BuildLoggerFromConfiguratorTest extends AbstractPluginLoggerTest
{
    public function test_build_empty_logger(): void
    {
        $logger = $this->facade->buildLogger($this->configurator);

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_should_not_log_without_logFile(): void
    {
        $this->configurator->setPluginClassCollection([
            StreamLoggerPluginFormatterStub::class,
        ]);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->setPluginClassCollection([
                StreamLoggerPluginFormatterStub::class,
            ])
            ->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log_extra(): void
    {
        $this->configurator
            ->setPluginClassCollection([
                StreamLoggerPluginFormatterStub::class,
            ])
            ->setProcessorClassCollection([
                MemoryUsageProcessorStub::class,
            ])
            ->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertLoggerFile('foo bar', 'info', [], ['memory_peak_usage' => '5 MB'], 0);
        $this->assertLoggerFile('foo bar warning', 'warning', [], ['memory_peak_usage' => '5 MB'], 1);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator
            ->setPluginClassCollection([
                StreamLoggerPluginFormatterStub::class,
            ])
            ->setProcessorClassCollection([
                MemoryUsageProcessorStub::class,
            ])
            ->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }
}
