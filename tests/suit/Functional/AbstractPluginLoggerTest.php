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
}
