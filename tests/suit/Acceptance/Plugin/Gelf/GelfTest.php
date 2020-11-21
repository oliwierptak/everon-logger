<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Acceptance\Plugin\Gelf;

use Everon\Logger\Configurator\Plugin\GelfLoggerPluginConfigurator;
use Everon\Logger\Configurator\Plugin\LoggerConfigurator;
use Everon\Logger\EveronLoggerFacade;
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
    }

    public function test_gelf_http(): void
    {
        $gelfPluginConfigurator = (new GelfLoggerPluginConfigurator())->getHttpConfigurator()
            ->setLogLevel('info')
            ->setHost($this->graylogHost)
            ->setPort(12202);

        $configurator = (new LoggerConfigurator())
            ->addPluginConfigurator($gelfPluginConfigurator);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum http');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_gelf_http_should_throw_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('@^Failed to create socket-client for ssl://(.*)@');

        $gelfPluginConfigurator = (new GelfLoggerPluginConfigurator())->getHttpConfigurator();
        $gelfPluginConfigurator
            ->setIgnoreTransportErrors(false)
            ->setLogLevel('info')
            ->setHost($this->graylogHost)
            ->setPort(12202)
            ->getSslOptions()
            ->setUseSsl(true);

        $configurator = (new LoggerConfigurator())
            ->addPluginConfigurator($gelfPluginConfigurator);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum http');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_gelf_udp(): void
    {
        $gelfPluginConfigurator = (new GelfLoggerPluginConfigurator())->getUdpConfigurator()
            ->setLogLevel('info')
            ->setHost($this->graylogHost);

        $configurator = (new LoggerConfigurator())
            ->addPluginConfigurator($gelfPluginConfigurator);

        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum udp');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_gelf_tcp(): void
    {
        $gelfPluginConfigurator = (new GelfLoggerPluginConfigurator())->getTcpConfigurator()
            ->setLogLevel('info')
            ->setHost($this->graylogHost)
            ->setPort(5555);

        $configurator = (new LoggerConfigurator())
            ->addPluginConfigurator($gelfPluginConfigurator);


        $logger = (new EveronLoggerFacade())->buildLogger($configurator);

        $logger->info('lorem ipsum tcp');

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
