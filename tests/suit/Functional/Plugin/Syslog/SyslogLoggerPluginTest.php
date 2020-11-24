<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Syslog;

use Everon\Logger\Configurator\Plugin\SyslogLoggerPluginConfigurator;
use Everon\Logger\Plugin\Syslog\SyslogLoggerPlugin;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Configurator\TestLoggerConfigurator;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;
use function shell_exec;

class SyslogLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $syslogPluginConfigurator = (new SyslogLoggerPluginConfigurator())
            ->setPluginClass(SyslogLoggerPlugin::class)
            ->setLogLevel('debug');

        $this->configurator->addPluginConfigurator($syslogPluginConfigurator);

        shell_exec('truncate -s 0 ' . $this->logFilename);
    }

    public function test_should_not_log_without_ident(): void
    {
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyLogFile();
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(SyslogLoggerPlugin::class)
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyLogFile();
    }

    public function test_should_log(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(SyslogLoggerPlugin::class)
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info'));

        $logger->warning('foo bar warning');
        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar warning')
            ->setLevel('warning'));
    }

    public function test_should_log_context(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(SyslogLoggerPlugin::class)
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum']));
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator
            ->addProcessorClass(MemoryUsageProcessorStub::class)
            ->getConfiguratorByPluginName(SyslogLoggerPlugin::class)
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum'])
            ->setExtra(['memory_peak_usage' => '5 MB']));
    }
}
