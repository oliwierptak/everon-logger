<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional;

use Everon\Logger\Configurator\Plugin\LoggerPluginConfigurator;
use Everon\Logger\EveronLoggerFacade;
use PHPUnit\Framework\TestCase;

abstract class AbstractPluginLoggerTest extends TestCase
{
    protected string $logFilename = '/tmp/everon-logger-plugin-logfile.log';

    protected LoggerPluginConfigurator $configurator;

    protected EveronLoggerFacade $facade;

    protected function setUp(): void
    {
        $this->configurator = new LoggerPluginConfigurator();
        $this->facade = new EveronLoggerFacade();

        @unlink($this->logFilename);
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
