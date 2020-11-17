<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Builder;

use Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginStub;
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
        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator())
            ->setPluginClass(StreamLoggerPluginStub::class);

        $this->configurator->addPluginConfigurator($streamPluginConfigurator);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator->addPluginConfigurator($streamPluginConfigurator);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log_extra(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(MemoryUsageProcessorStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertLoggerFile('foo bar', 'info', [], ['memory_peak_usage' => '5 MB'], 0);
        $this->assertLoggerFile('foo bar warning', 'warning', [], ['memory_peak_usage' => '5 MB'], 1);
    }

    public function test_should_log_context_and_extra(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(MemoryUsageProcessorStub::class);

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
