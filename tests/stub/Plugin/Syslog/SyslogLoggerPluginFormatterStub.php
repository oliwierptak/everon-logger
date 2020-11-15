<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Syslog;

use Everon\Logger\Contract\Plugin\LoggerPluginFormatterInterface;
use Everon\Logger\Plugin\Syslog\SyslogLoggerPlugin;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;

class SyslogLoggerPluginFormatterStub extends SyslogLoggerPlugin implements LoggerPluginFormatterInterface
{
    public function buildFormatter(): FormatterInterface
    {
        return new LineFormatter("%level_name% - %message% %context%", 'H:i:s');
    }
}
