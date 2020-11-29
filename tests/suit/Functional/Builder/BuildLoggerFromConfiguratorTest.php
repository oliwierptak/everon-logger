<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Builder;

use Everon\Logger\Exception\ConfiguratorValidationException;
use Everon\Logger\Exception\HandlerBuildException;
use Everon\Logger\Exception\PluginBuildException;
use Everon\Logger\Exception\ProcessorBuildException;
use EveronLoggerTests\Stub\Plugin\Stream\HandlerExceptionLoggerPluginStub;
use EveronLoggerTests\Stub\Plugin\Stream\PluginExceptionLoggerPluginStub;
use EveronLoggerTests\Stub\Plugin\Stream\ProcessorExceptionStub;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginConfiguratorStub;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Configurator\TestLoggerConfigurator;
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
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class);

        $this->configurator->addPluginConfigurator($streamPluginConfigurator);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
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
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(MemoryUsageProcessorStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setExtra(['memory_peak_usage' => '5 MB']));

        $logger->warning('foo bar warning');
        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar warning')
            ->setLevel('warning')
            ->setExtra(['memory_peak_usage' => '5 MB']));
    }

    public function test_should_log_context_and_extra(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(MemoryUsageProcessorStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum'])
            ->setExtra(['memory_peak_usage' => '5 MB']));
    }

    public function test_build_should_throw_handler_exception(): void
    {
        $this->expectException(HandlerBuildException::class);
        $this->expectExceptionMessage('Could not build handler for plugin: "EveronLoggerTests\Stub\Plugin\Stream\HandlerExceptionLoggerPluginStub". Error: Invalid value for foo bar');

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(HandlerExceptionLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }

    public function test_build_should_throw_plugin_exception(): void
    {
        $this->expectException(PluginBuildException::class);
        $this->expectExceptionMessage('Could not build plugin: "EveronLoggerTests\Stub\Plugin\Stream\PluginExceptionLoggerPluginStub". Error: Invalid value for foo bar');

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(PluginExceptionLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }

    public function test_build_should_throw_processor_exception(): void
    {
        $this->expectException(ProcessorBuildException::class);
        $this->expectExceptionMessage('Could not build processor: "EveronLoggerTests\Stub\Plugin\Stream\ProcessorExceptionStub". Error: Invalid value for foo bar');

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel('info')
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(ProcessorExceptionStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }

    public function test_build_should_throw_configuration_exception(): void
    {
        $this->expectException(ConfiguratorValidationException::class);
        $this->expectExceptionMessage('Required value of "name" has not been set');

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub());

        $this->configurator
            ->addPluginConfigurator($streamPluginConfigurator)
            ->setName(null);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }
}
