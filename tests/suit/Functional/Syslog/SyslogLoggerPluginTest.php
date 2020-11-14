<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Syslog;

use Everon\Logger\Configurator\LoggerPluginConfigurator;
use Everon\Logger\Contract\Container\LoggerContainerInterface;
use Everon\Logger\EveronLoggerFacade;
use EveronLoggerTests\Stub\PluginContainerStub;
use PHPUnit\Framework\TestCase;
use function shell_exec;

class SyslogLoggerPluginTest extends TestCase
{
    protected string $logFilename = '/var/log/syslog';

    protected LoggerPluginConfigurator $configurator;

    protected LoggerContainerInterface $pluginContainer;

    protected EveronLoggerFacade $facade;

    protected function setUp(): void
    {
        $this->configurator = new LoggerPluginConfigurator();
        $this->pluginContainer = new PluginContainerStub($this->configurator);

        $this->facade = new EveronLoggerFacade();

        shell_exec('truncate -s 0 ' . $this->logFilename);
    }

    public function test_should_not_log_without_ident(): void
    {
        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertEmptySyslog();
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator->getSyslogConfigurator()
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertEmptySyslog();
    }

    public function test_should_log(): void
    {
        $this->configurator->getSyslogConfigurator()
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->info('foo bar');
        $this->assertSyslog('foo bar', 'info', []);

        $logger->warning('foo bar warning');
        $this->assertSyslog('foo bar warning', 'warning', []);
    }

    public function test_should_log_context(): void
    {
        $this->configurator->getSyslogConfigurator()
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger(
            $this->configurator,
            $this->pluginContainer
        );

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertSyslog('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    protected function assertSyslog(string $message, string $level, array $context = []): void
    {
        $syslogData = shell_exec('tail --lines=1 ' . $this->logFilename);
        if ($syslogData === null) {
            $this->assertFalse(false);

            return;
        };

        $jsonContextString = json_encode($context);
        $data = explode("\n", $syslogData);

        foreach ($data as $line) {
            $tokens = explode(']:', trim($line));
            if (count($tokens) < 2) {
                continue;
            }

            $expected = sprintf(
                '%s - %s %s',
                \strtoupper($level),
                $message,
                $jsonContextString
            );
            $this->assertEquals($expected, trim($tokens[1]));
        }
    }

    protected function assertEmptySyslog(): void
    {
        $syslogData = shell_exec('tail --lines=1 ' . $this->logFilename);
        $this->assertNull($syslogData);
    }
}
