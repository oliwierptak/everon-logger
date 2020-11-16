<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Contract\Plugin\PluginFormatterInterface;
use Everon\Logger\Plugin\Stream\StreamLoggerPlugin;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use const PHP_EOL;

class StreamLoggerPluginStub extends StreamLoggerPlugin implements PluginFormatterInterface
{
    public function buildFormatter(): FormatterInterface
    {
        return new LineFormatter("%level_name%: %message% %context% %extra%" . PHP_EOL, 'H:i:s');
    }
}
