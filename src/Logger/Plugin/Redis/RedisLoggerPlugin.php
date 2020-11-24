<?php

declare(strict_types = 1);

namespace Everon\Logger\Plugin\Redis;

use Everon\Logger\Configurator\Plugin\RedisLoggerPluginConfigurator;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RedisHandler;
use Monolog\Logger;
use Redis;

class RedisLoggerPlugin implements LoggerPluginInterface
{
    protected RedisLoggerPluginConfigurator $configurator;

    public function __construct(RedisLoggerPluginConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function canRun(): bool
    {
        return $this->configurator->hasKey();
    }

    public function buildHandler(): HandlerInterface
    {
        $this->validate();

        return new RedisHandler(
            $this->buildRedis(),
            $this->configurator->getKey(),
            Logger::toMonologLevel($this->configurator->requireLogLevel()),
            $this->configurator->shouldBubble(),
            $this->configurator->getCapSize(),
        );
    }

    protected function validate(): void
    {
        $this->configurator->requireKey();
        $this->configurator->requireLogLevel();
        $this->configurator->requireCapSize();
        $this->configurator->requireLogLevel();

        $this->configurator->getRedisConnection()->requireHost();
        $this->configurator->getRedisConnection()->requirePort();
        $this->configurator->getRedisConnection()->requireTimeout();
        $this->configurator->getRedisConnection()->requireRetryInterval();
        $this->configurator->getRedisConnection()->requireReadTimeout();
    }

    protected function buildRedis(): Redis
    {
        $redis = new Redis();

        if ($this->configurator->getRedisConnection()->getPersistentId() !== null) {
            $redis->pconnect(
                $this->configurator->getRedisConnection()->getHost(),
                $this->configurator->getRedisConnection()->getPort(),
                $this->configurator->getRedisConnection()->getTimeout(),
                $this->configurator->getRedisConnection()->getPersistentId(),
                $this->configurator->getRedisConnection()->getRetryInterval(),
                $this->configurator->getRedisConnection()->getReadTimeout()
            );
        }
        else {
            $redis->connect(
                $this->configurator->getRedisConnection()->getHost(),
                $this->configurator->getRedisConnection()->getPort(),
                $this->configurator->getRedisConnection()->getTimeout(),
                null,
                $this->configurator->getRedisConnection()->getRetryInterval(),
                $this->configurator->getRedisConnection()->getReadTimeout()
            );
        }

        return $redis;
    }
}
