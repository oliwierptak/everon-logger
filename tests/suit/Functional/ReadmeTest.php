<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator;
use Everon\Logger\Configurator\Plugin\SyslogLoggerPluginConfigurator;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ReadmeTest extends TestCase
{
    protected function setUp(): void
    {
        @unlink('/tmp/example.log');
    }

    public function test_build_logger(): void
    {
        $configurator = (new LoggerPluginConfigurator())->addPluginConfigurator(
            (new StreamLoggerPluginConfigurator)
                ->setLogLevel('info')
                ->setStreamLocation('/tmp/example.log')
        );

        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator)
            ->setLogLevel('info')
            ->setStreamLocation('/tmp/example.log');

        $configurator = (new LoggerPluginConfigurator())
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(MemoryUsageProcessorStub::class);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertFileExists('/tmp/example.log');
    }

    public function test_build_logger2(): void
    {
        $configurator = (new LoggerPluginConfigurator())
            ->addProcessorClass(MemoryUsageProcessor::class)
            ->addProcessorClass(HostnameProcessor::class)
            ->addPluginConfigurator(
                (new StreamLoggerPluginConfigurator)
                    ->setLogLevel('debug')
                    ->setStreamLocation('/tmp/example.log')
            )->addPluginConfigurator(
                (new SyslogLoggerPluginConfigurator())
                    ->setLogLevel('info')
                    ->setIdent('everon-logger-ident')
            );

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->debug('lorem ipsum');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertFileExists('/tmp/example.log');
    }

    public function test_build_logger3(): void
    {
        $configurator = (new LoggerPluginConfigurator())
            ->addProcessorClass(MemoryUsageProcessor::class)
            ->addProcessorClass(HostnameProcessor::class)
            ->addPluginConfigurator(
                (new StreamLoggerPluginConfigurator)
                    ->setLogLevel('debug')
                    ->setStreamLocation('/tmp/example.log')
            )->addPluginConfigurator(
                (new SyslogLoggerPluginConfigurator())
                    ->setLogLevel('info')
                    ->setIdent('everon-logger-ident')
            );

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->debug('lorem ipsum');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertFileExists('/tmp/example.log');
    }
}
