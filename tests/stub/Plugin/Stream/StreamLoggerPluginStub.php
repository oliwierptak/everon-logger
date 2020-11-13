<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use Everon\Logger\Contract\Plugin\LoggerFormatterPluginInterface;
use Everon\Logger\Plugin\Stream\StreamLoggerPlugin;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;

class StreamLoggerPluginStub extends StreamLoggerPlugin implements LoggerFormatterPluginInterface
{
    public function buildFormatter(): FormatterInterface
    {
        return new LineFormatter("%level_name%: %message% %context%" . \PHP_EOL, 'H:i:s');
    }
}
