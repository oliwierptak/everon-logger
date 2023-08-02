<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Processor;

use Monolog\LogRecord;
use Monolog\Processor\MemoryProcessor;

class MemoryUsageProcessorStub extends MemoryProcessor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $usage = 1024 * 1024 * 5;

        if ($this->useFormatting) {
            $usage = $this->formatBytes($usage);
        }

        $record['extra']['memory_peak_usage'] = $usage;

        return $record;
    }
}
