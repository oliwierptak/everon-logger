<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Redis;

use Everon\Logger\Contract\Plugin\PluginFormatterInterface;
use Everon\Logger\Plugin\Redis\RedisLoggerPlugin;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;

class RedisLoggerPluginStub extends RedisLoggerPlugin implements PluginFormatterInterface
{
    public function buildFormatter(): FormatterInterface
    {
        return new LineFormatter("%level_name%: %message% %context% %extra%", 'H:i:s');
    }
}
