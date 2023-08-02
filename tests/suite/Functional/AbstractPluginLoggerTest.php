<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suite\Functional;

use Everon\Logger\Configurator\Plugin\LoggerConfigurator;
use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Suite\Configurator\TestLoggerConfigurator;
use PHPUnit\Framework\TestCase;
use function addslashes;
use function is_file;
use function preg_split;

/**
 * @group skip
 */
abstract class AbstractPluginLoggerTest extends TestCase
{
    protected string $logFilename = '/tmp/everon-logger-plugin-logfile.log';

    protected LoggerConfiguratorInterface $configurator;

    protected EveronLoggerFacade $facade;

    protected function setUp(): void
    {
        $this->configurator = new LoggerConfigurator();
        $this->facade = new EveronLoggerFacade();

        @unlink($this->logFilename);
    }

    protected function assertEmptyLogFile(): void
    {
        $syslogData = shell_exec('tail -n 1 ' . $this->logFilename);
        $this->assertNull($syslogData);
    }

    protected function assertLogFile(TestLoggerConfigurator $configurator): void
    {
        if (!is_file($this->logFilename)) {
            return;
        }
        
        $logData = shell_exec('tail -n 1 ' . $this->logFilename);
        if ($logData === null) {
            $this->assertFalse(false);

            return;
        }

        $jsonContextString = json_encode($configurator->getContext());
        $jsonExtraString = json_encode($configurator->getExtra());
        $data = explode("\n", $logData);

        foreach ($data as $line) {
            //[2020-11-21T14:25:08.720572+00:00] everon-logger.INFO: foo bar [] []
            $tokens = preg_split('@' . addslashes($configurator->getDelimiter()) . '@', trim($line));
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
