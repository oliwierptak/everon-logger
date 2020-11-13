<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Stream;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Stub\PluginContainerStub;
use PHPUnit\Framework\TestCase;

class StreamLoggerPluginTest extends TestCase
{
    protected string $logFilename = '/tmp/everon-logger-plugin-logfile.log';

    protected LoggerPluginConfigurator $configurator;

    protected LoggerContainerInterface $pluginContainer;

    protected EveronLoggerFacade $facade;

    protected function setUp(): void
    {
        $this->configurator = new LoggerPluginConfigurator();
        $this->pluginContainer = new PluginContainerStub($this->configurator);

        $this->facade = new EveronLoggerFacade();

        @unlink($this->logFilename);
    }

    public function test_should_not_log_without_logFile(): void
    {
        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->setLogLevel('info')
            ->getStreamConfigurator()
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertFileDoesNotExist($this->logFilename);
    }

    public function test_should_log(): void
    {
        $this->configurator
            ->setLogLevel('info')
            ->getStreamConfigurator()
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertLoggerFile('foo bar', 'info', [], 0);
        $this->assertLoggerFile('foo bar warning', 'warning', [], 1);
    }

    public function test_should_log_context(): void
    {
        $this->configurator
            ->setLogLevel('info')
            ->getStreamConfigurator()
            ->setStreamLocation($this->logFilename);

        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLoggerFile('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    protected function assertLoggerFile(string $message, string $level, array $context = [], int $index = 0): void
    {
        $jsonContextString = json_encode($context);

        $expected = sprintf(
            '%s: %s %s' . \PHP_EOL,
            \strtoupper($level),
            $message,
            $jsonContextString
        );

        $this->assertFileExists($this->logFilename);
        $this->assertEquals($expected, file($this->logFilename)[$index]);
    }
}
