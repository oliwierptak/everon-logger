<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Suit\Functional\Plugin\Redis;

use EveronLoggerTests\Stub\Plugin\Redis\RedisLoggerPluginStub;
use EveronLoggerTests\Stub\Processor\MemoryUsageProcessorStub;
use EveronLoggerTests\Suit\Functional\AbstractPluginLoggerTest;
use Redis;

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
        $this->configurator
            ->addPluginClass(RedisLoggerPluginStub::class)
            ->getRedisConfigurator()
            ->setLogLevel('info');

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyRedis();
    }

    protected function assertEmptyRedis(): void
    {
        $this->assertTrue(true);
    }

    public function test_should_not_log_when_level_too_low(): void
    {
        $this->configurator
            ->addPluginClass(RedisLoggerPluginStub::class)
            ->getRedisConfigurator()
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE)
            ->getRedisConnection()
            ->setHost($this->redisHost)
            ->setPort($this->redisPort);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->debug('foo bar');

        $this->assertEmptyRedis();
    }

    public function test_should_log(): void
    {
        $this->configurator
            ->addPluginClass(RedisLoggerPluginStub::class)
            ->getRedisConfigurator()
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE)
            ->getRedisConnection()
            ->setHost($this->redisHost)
            ->setPort($this->redisPort);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar');
        $this->assertRedis('foo bar', 'info', []);

        $logger->warning('foo bar warning');
        $this->assertRedis('foo bar warning', 'warning', []);
    }

    public function test_should_log_context(): void
    {
        $this->configurator
            ->addPluginClass(RedisLoggerPluginStub::class)
            ->getRedisConfigurator()
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE)
            ->getRedisConnection()
            ->setHost($this->redisHost)
            ->setPort($this->redisPort);

        $logger = $this->facade->buildLogger($this->configurator);

        $logger->info('foo bar', ['buzz' => 'lorem ipsum']);

        $this->assertRedis('foo bar', 'info', ['buzz' => 'lorem ipsum']);
    }

    public function test_should_log_context_and_extra(): void
    {
        $this->configurator
            ->addPluginClass(RedisLoggerPluginStub::class)
            ->addProcessorClass(MemoryUsageProcessorStub::class)
            ->getRedisConfigurator()
            ->setLogLevel('info')
            ->setKey(static::REDIS_QUEUE)
            ->getRedisConnection()
            ->setHost($this->redisHost)
            ->setPort($this->redisPort);

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
}
