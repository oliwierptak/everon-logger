<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Acceptance\Plugin;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\EveronLoggerFacade;
use Everon\Logger\Plugin\GelfHttp\GelfHttpLoggerPlugin;
use Everon\Logger\Plugin\GelfTcp\GelfTcpLoggerPlugin;
use Everon\Logger\Plugin\GelfUdp\GelfUdpLoggerPlugin;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @group acceptance
 */
class GelfTest extends TestCase
{
    protected string $graylogHost;

    protected function setUp(): void
    {
        $this->graylogHost = $_ENV['TEST_GELF_HOST'];

        @unlink('/tmp/everon-logger-gelf.log');
    }

    public function test_gelf_http(): void
    {
        $configurator = (new LoggerPluginConfigurator())
            ->addPluginClass(GelfHttpLoggerPlugin::class);

        $configurator
            ->getGelfConfigurator()
            ->getHttpConfigurator()
            ->setLogLevel('info')
            ->setHost($this->graylogHost)
            ->setPort(12202);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum http');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_gelf_http_should_throw_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('@^Failed to create socket-client for ssl://(.*)@');

        $configurator = (new LoggerPluginConfigurator())
            ->addPluginClass(GelfHttpLoggerPlugin::class);

        $configurator
            ->getGelfConfigurator()
            ->setIgnoreTransportErrors(false)
            ->getHttpConfigurator()
            ->setLogLevel('info')
            ->setUseSsl(true)
            ->setHost($this->graylogHost)
            ->setPort(12202);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum http');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_gelf_udp(): void
    {
        $configurator = (new LoggerPluginConfigurator())
            ->addPluginClass(GelfUdpLoggerPlugin::class);

        $configurator
            ->getGelfConfigurator()
            ->getUdpConfigurator()
            ->setLogLevel('info')
            ->setHost($this->graylogHost);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum udp');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_gelf_tcp(): void
    {
        $configurator = (new LoggerPluginConfigurator())
            ->addPluginClass(GelfTcpLoggerPlugin::class);

        $configurator
            ->getGelfConfigurator()
            ->setIgnoreTransportErrors(false)
            ->getTcpConfigurator()
            ->setLogLevel('info')
            ->setHost($this->graylogHost)
            ->setPort(5555);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum tcp');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
