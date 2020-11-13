<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Stub\PluginContainerStub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FacadeTest extends TestCase
{
    public function test_build_logger(): void
    {
        $configurator = new LoggerPluginConfigurator();
        $pluginContainer = new PluginContainerStub($configurator);

        $facade = new EveronLoggerFacade();

        $logger = $facade->buildLogger(
            $configurator,
            $pluginContainer
        );

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
