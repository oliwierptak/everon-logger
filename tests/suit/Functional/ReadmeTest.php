<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\EveronLoggerFacade;
use Everon\Logger\Plugin\Stream\StreamLoggerPlugin;
use Monolog\Processor\MemoryUsageProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ReadmeTest extends TestCase
{
    protected function setUp(): void
    {
        @unlink('/tmp/everon-logger-example.log');
    }

    public function test_build_logger(): void
    {
        $configurator = (new LoggerPluginConfigurator())
            ->addPluginClass(StreamLoggerPlugin::class)
            ->addProcessorClass(MemoryUsageProcessor::class);

        $configurator
            ->getStreamConfigurator()
            ->setLogLevel('info')
            ->setStreamLocation('/tmp/everon-logger-example.log');

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertFileExists('/tmp/everon-logger-example.log');
    }
}
