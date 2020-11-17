<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\Configurator\Plugin\StreamLoggerPluginConfigurator;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
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
        $streamPluginConfigurator = (new StreamLoggerPluginConfigurator)
            ->setLogLevel('info')
            ->setStreamLocation('/tmp/everon-logger-example.log');

        $configurator = (new LoggerPluginConfigurator())
            ->addPluginConfigurator($streamPluginConfigurator)
            ->addProcessorClass(MemoryUsageProcessorStub::class);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertFileExists('/tmp/everon-logger-example.log');
    }
}
