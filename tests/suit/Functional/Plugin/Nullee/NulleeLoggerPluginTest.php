<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Nullee;

use Everon\Logger\Configurator\Plugin\NulleeLoggerPluginConfigurator;
use Everon\Logger\Plugin\Nullee\NulleeLoggerPlugin;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;

class NulleeLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $nulleePluginConfigurator = (new NulleeLoggerPluginConfigurator())
            ->setLogLevel('debug');

        $this->configurator->addPluginConfigurator($nulleePluginConfigurator);
    }

    public function test_should_not_log(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(NulleeLoggerPlugin::class)
            ->setLogLevel('info');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $logger->warning('foo bar warning');

        $this->assertEmptyLogFile();
    }
}
