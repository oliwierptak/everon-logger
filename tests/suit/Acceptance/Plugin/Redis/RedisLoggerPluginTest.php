<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Acceptance\Plugin\Redis;

use Everon\Logger\Configurator\Plugin\RedisLoggerPluginConfigurator;
use Everon\Logger\Plugin\Redis\RedisLoggerPlugin;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;
use Redis;

/**
 * @group acceptance
 */
class RedisLoggerPluginTest extends AbstractPluginLoggerTest
{
    protected const REDIS_QUEUE = 'everon-redis-queue';

    protected Redis $redis;

    protected string $redisHost;

    protected int $redisPort;

    protected function setUp(): void
    {
        parent::setUp();

        $this->redisHost = $_ENV['TEST_REDIS_HOST'];
        $this->redisPort = (int) $_ENV['TEST_REDIS_PORT'];

        $this->getRedis()->flushAll();

        $redisPluginConfigurator = (new RedisLoggerPluginConfigurator())
            ->setPluginClass(RedisLoggerPlugin::class)
            ->setLogLevel('debug');

        $redisPluginConfigurator
            ->getRedisConnection()
            ->setHost($this->redisHost)
            ->setPort($this->redisPort)
            ->setTimeout(10);

        $this->configurator->addPluginConfigurator($redisPluginConfigurator);
    }

    public function getRedis(): \Redis
    {
        if (empty($this->redis)) {
            $this->redis = new Redis();
            $this->redis->connect(
                $this->redisHost,
                $this->redisPort,
                10
            );
        }

        return $this->redis;
    }

    public function test_should_not_log_without_key(): void
    {
        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyRedis();
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(RedisLoggerPlugin::class)
            ->setLogLevel('info');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyRedis();
    }

    public function test_should_log(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(RedisLoggerPlugin::class)
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $this->assertRedis('foo bar', 'info', []);

        $logger->warning('foo bar warning');
        $this->assertRedis('foo bar warning', 'warning', []);
    }

    public function test_should_log_context(): void
    {
        $this->configurator
            ->getConfiguratorByPluginName(RedisLoggerPlugin::class)
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertRedis('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator
            ->addProcessorClass(MemoryUsageProcessorStub::class)
            ->getConfiguratorByPluginName(RedisLoggerPlugin::class)
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertRedis('foo bar', 'info', ['buzz' => 'lorem ipsum'], ['memory_peak_usage' => '5 MB']);
    }

    protected function assertRedis(string $message, string $level, array $context = [], array $extra = []): void
    {
        $jsonContextString = json_encode($context);
        $jsonExtraString = json_encode($extra);

        $expected = sprintf(
            '%s: %s %s %s',
            \strtoupper($level),
            $message,
            $jsonContextString,
            $jsonExtraString
        );

        $data = $this->getRedis()->lRange(static::REDIS_QUEUE, 0, -1);
        $line = array_pop($data);

        $this->assertEquals($expected, $line);
    }

    protected function assertEmptyRedis(): void
    {
        $data = $this->getRedis()->lRange(static::REDIS_QUEUE, 0, -1);

        $this->assertEmpty($data);
    }
}
