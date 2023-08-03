<?php declare(strict_types = 1);

namespace Everon\Shared\Testify\Logger;

use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\EveronLoggerFacade;
use Everon\Shared\Logger\Configurator\Plugin\LoggerConfigurator;
use EveronLoggerTests\Suite\Configurator\TestLoggerConfigurator;

trait LoggerHelperTrait
{
    protected string $logFilename = '/tmp/everon-logger-plugin-logfile.log';

    protected LoggerConfiguratorInterface $configurator;

    protected EveronLoggerFacade $facade;

    protected function init(): void
    {
        $this->configurator = new LoggerConfigurator();
        $this->facade = new EveronLoggerFacade();

        @unlink($this->logFilename);
    }

    protected function assertEmptyLogFile(): void
    {
        $syslogData = (is_file($this->logFilename)) ? shell_exec('tail -n 1 ' . $this->logFilename) : null;
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
                strtoupper($configurator->getLogLevel()->getName()),
                $configurator->getMessage(),
                $jsonContextString,
                $jsonExtraString,
            );
            $this->assertEquals($expected, trim($tokens[1]));
        }
    }
}