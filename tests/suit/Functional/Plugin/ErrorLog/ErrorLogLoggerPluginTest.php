<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\ErrorLog;

use Everon\Logger\Configurator\Plugin\ErrorLogLoggerPluginConfigurator;
use Everon\Logger\Plugin\ErrorLog\ErrorLogLoggerPlugin;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Configurator\TestLoggerConfigurator;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;

class ErrorLogLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        ini_set('error_log', $this->logFilename);

        $syslogPluginConfigurator = (new ErrorLogLoggerPluginConfigurator())
            ->setPluginClass(ErrorLogLoggerPlugin::class)
            ->setLogLevel('debug')
            ->setMessageType(\Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM)
            ->setExpandNewlines(false);

        $this->configurator->addPluginConfigurator($syslogPluginConfigurator);
    }

    public function test_should_not_log_without_message_type(): void
    {
        $this->expectException(\Everon\Logger\Exception\HandlerBuildException::class);
        $this->expectExceptionMessage('Could not build handler in plugin: "Everon\Logger\Plugin\ErrorLog\ErrorLogLoggerPlugin". Error: Required value of "messageType" has not been set');

        $this->configurator
            ->getPluginConfiguratorByPluginName(ErrorLogLoggerPlugin::class)
            ->setMessageType(null);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->getPluginConfiguratorByPluginName(ErrorLogLoggerPlugin::class)
            ->setLogLevel('info');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyLogFile();
    }

    public function test_should_log(): void
    {
        $this->configurator
            ->getPluginConfiguratorByPluginName(ErrorLogLoggerPlugin::class)
            ->setLogLevel('info');

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
            ->getPluginConfiguratorByPluginName(ErrorLogLoggerPlugin::class)
            ->setLogLevel('info');

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
            ->getPluginConfiguratorByPluginName(ErrorLogLoggerPlugin::class)
            ->setLogLevel('info');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertLogFile((new TestLoggerConfigurator())
            ->setMessage('foo bar')
            ->setLevel('info')
            ->setContext(['buzz' => 'lorem ipsum'])
            ->setExtra(['memory_peak_usage' => '5 MB']));
    }
}
