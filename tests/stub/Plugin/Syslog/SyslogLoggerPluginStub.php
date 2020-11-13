<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Syslog;

use Everon\Logger\Contract\Plugin\LoggerFormatterPluginInterface;
use Everon\Logger\Plugin\Syslog\SyslogLoggerPlugin;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;

class SyslogLoggerPluginStub extends SyslogLoggerPlugin implements LoggerFormatterPluginInterface
{
    public function buildFormatter(): FormatterInterface
    {
        return new LineFormatter("%level_name% - %message% %context%", 'H:i:s');
    }
}
