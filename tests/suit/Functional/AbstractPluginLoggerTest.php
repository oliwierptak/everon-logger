<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional;

use Everon\Logger\Configurator\Plugin\LoggerConfigurator;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Suit\Configurator\TestLoggerConfigurator;
use PHPUnit\Framework\TestCase;

abstract class AbstractPluginLoggerTest extends TestCase
{
    protected string $logFilename = '/tmp/everon-logger-plugin-logfile.log';

    protected LoggerConfigurator $configurator;

    protected EveronLoggerFacade $facade;

    protected function setUp(): void
    {
        $this->configurator = new LoggerConfigurator();
        $this->facade = new EveronLoggerFacade();

        @unlink($this->logFilename);
    }

    protected function assertEmptyLogFile(): void
    {
        $syslogData = shell_exec('tail --lines=1 ' . $this->logFilename);
        $this->assertNull($syslogData);
    }

    //protected function assertLogFile(string $message, string $level, array $context = [], array $extra = []): void
    protected function assertLogFile(TestLoggerConfigurator $configurator): void
    {
        $logData = shell_exec('tail --lines=1 ' . $this->logFilename);
        if ($logData === null) {
            $this->assertFalse(false);

            return;
        }

        $jsonContextString = json_encode($configurator->getContext());
        $jsonExtraString = json_encode($configurator->getExtra());
        $data = explode("\n", $logData);

        foreach ($data as $line) {
            //[2020-11-21T14:25:08.720572+00:00] everon-logger.INFO: foo bar [] []
            $tokens = \preg_split('@' . \addslashes($configurator->getDelimiter()) . '@', trim($line));
            if (count($tokens) < 2) {
                continue;
            }

            $expected = sprintf(
                '%s: %s %s %s',
                strtoupper($configurator->getLevel()),
                $configurator->getMessage(),
                $jsonContextString,
                $jsonExtraString
            );
            $this->assertEquals($expected, trim($tokens[1]));
        }
    }
}
