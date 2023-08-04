<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suite\Functional\Builder;

use Everon\Logger\Exception\ConfiguratorValidationException;
use Everon\Logger\Exception\HandlerBuildException;
use Everon\Logger\Exception\PluginBuildException;
use Everon\Logger\Exception\ProcessorBuildException;
use Everon\Shared\Testify\Logger\LoggerHelperTrait;
use EveronLoggerTests\Stub\Plugin\Stream\FactoryStub;
use EveronLoggerTests\Stub\Plugin\Stream\PluginExceptionLoggerPluginStub;
use EveronLoggerTests\Stub\Plugin\Stream\ProcessorExceptionStub;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginConfiguratorStub;
use EveronLoggerTests\Stub\Plugin\Stream\StreamLoggerPluginStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suite\Configurator\TestLoggerConfigurator;
use Monolog\Level;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BuildLoggerFromConfiguratorTest extends TestCase
{
    use LoggerHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->init();
    }

    public function test_build_empty_logger(): void
    {
        $logger = $this->facade->buildLogger($this->configurator);

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_should_validate_builder_configuration(): void
    {
        $this->expectException(ConfiguratorValidationException::class);
        $this->expectExceptionMessage('Required value of "streamLocation" has not been set');

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class);

        $this->configurator
            ->setValidateConfiguration(true)
            ->add($streamPluginConfigurator);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_without_logFile(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class);

        $this->configurator
            ->setValidateConfiguration(false)
            ->add($streamPluginConfigurator);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel(Level::Info)
            ->setStreamLocation($this->logFilename);

        $this->configurator->add($streamPluginConfigurator);
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log_extra(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel(Level::Info)
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->add($streamPluginConfigurator)
            ->addProcessor(MemoryUsageProcessorStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $this->assertLogFile(
            (new TestLoggerConfigurator())
                ->setMessage('foo bar')
                ->setLogLevel(Level::Info)
                ->setExtra(['memory_peak_usage' => '5 MB']),
        );

        $logger->warning('foo bar warning');
        $this->assertLogFile(
            (new TestLoggerConfigurator())
                ->setMessage('foo bar warning')
                ->setLogLevel(Level::Warning)
                ->setExtra(['memory_peak_usage' => '5 MB']),
        );
    }

    public function test_should_log_context_and_extra(): void
    {
        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel(Level::Info)
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->add($streamPluginConfigurator)
            ->addProcessor(MemoryUsageProcessorStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile(
            (new TestLoggerConfigurator())
                ->setMessage('foo bar')
                ->setLogLevel(Level::Info)
                ->setContext(['buzz' => 'lorem ipsum'])
                ->setExtra(['memory_peak_usage' => '5 MB']),
        );
    }

    public function test_build_should_throw_exception_when_validation_fails(): void
    {
        $this->expectException(HandlerBuildException::class);
        $this->expectExceptionMessage(
            'Could not build handler for plugin: "EveronLoggerTests\Stub\Plugin\Stream\HandlerExceptionLoggerPluginStub". Error: Invalid value for foo bar',
        );

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginFactoryClass(FactoryStub::class)
            ->setLogLevel(Level::Info)
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->add($streamPluginConfigurator);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }

    public function test_build_should_throw_plugin_exception(): void
    {
        $this->expectException(PluginBuildException::class);
        $this->expectExceptionMessage(
            'Could not build plugin: "EveronLoggerTests\Stub\Plugin\Stream\PluginExceptionLoggerPluginStub". Error: Invalid value for foo bar',
        );

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(PluginExceptionLoggerPluginStub::class)
            ->setLogLevel(Level::Info)
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->add($streamPluginConfigurator);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }

    public function test_build_should_throw_processor_exception(): void
    {
        $this->expectException(ProcessorBuildException::class);
        $this->expectExceptionMessage(
            'Could not build processor: "EveronLoggerTests\Stub\Plugin\Stream\ProcessorExceptionStub". Error: Invalid value for foo bar',
        );

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub())
            ->setPluginClass(StreamLoggerPluginStub::class)
            ->setLogLevel(Level::Info)
            ->setStreamLocation($this->logFilename);

        $this->configurator
            ->add($streamPluginConfigurator)
            ->addProcessor(ProcessorExceptionStub::class);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }

    public function test_build_should_throw_configuration_exception(): void
    {
        $this->expectException(ConfiguratorValidationException::class);
        $this->expectExceptionMessage('Required value of "name" has not been set');

        $streamPluginConfigurator = (new StreamLoggerPluginConfiguratorStub());

        $this->configurator
            ->add($streamPluginConfigurator)
            ->setName(null);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
    }
}
