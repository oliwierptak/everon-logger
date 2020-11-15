<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Syslog;

use Everon\Logger\Contract\Container\LoggerContainerInterface;
use EveronLoggerTests\Stub\Container\PluginContainerStub;
use EveronLoggerTests\Stub\Container\PluginContainerWithProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;
use function shell_exec;
use function strtoupper;
use const PHP_EOL;

class SyslogLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected LoggerContainerInterface $pluginContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pluginContainer = new PluginContainerStub($this->configurator);

        shell_exec('truncate -s 0 ' . $this->logFilename);
    }

    public function test_should_not_log_without_ident(): void
    {
        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->debug('foo bar');

        $this->assertEmptySyslog();
    }

    protected function assertEmptySyslog(): void
    {
        $syslogData = shell_exec('tail --lines=1 ' . $this->logFilename);
        $this->assertNull($syslogData);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator->getSyslogConfigurator()
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLoggerFromContainer(
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

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->info('foo bar');
        $this->assertSyslog('foo bar', 'info', []);

        $logger->warning('foo bar warning');
        $this->assertSyslog('foo bar warning', 'warning', []);
    }

    protected function assertSyslog(string $message, string $level, array $context = [], array $extra = []): void
    {
        $syslogData = shell_exec('tail --lines=1 ' . $this->logFilename);
        if ($syslogData === null) {
            $this->assertFalse(false);

            return;
        }

        $jsonContextString = json_encode($context);
        $jsonExtraString = json_encode($extra);
        $data = explode("\n", $syslogData);

        foreach ($data as $line) {
            $tokens = explode(']:', trim($line));
            if (count($tokens) < 2) {
                continue;
            }

            $expected = sprintf(
                '%s: %s %s %s' . PHP_EOL,
                strtoupper($level),
                $message,
                $jsonContextString,
                $jsonExtraString
            );
            $this->assertEquals($expected, trim($tokens[1]));
        }
    }

    public function test_should_log_context(): void
    {
        $this->configurator->getSyslogConfigurator()
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLoggerFromContainer(
            $this->pluginContainer
        );

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertSyslog('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->pluginContainer = new PluginContainerWithProcessorStub($this->configurator);

        $this->configurator->getSyslogConfigurator()
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLoggerFromContainer($this->pluginContainer);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertSyslog('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }
}
