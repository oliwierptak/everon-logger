<?php

declare(strict_types = 1);

namespace Everon\Logger\Contract\Plugin;

use Monolog\Formatter\FormatterInterface;

interface LoggerFormatterPluginInterface
{
    /**
     * Specification:
     * - Create instance of formatter implementing Monolog\Formatter\FormatterInterface
     * - Return formatter instance or null in case custom formatter is not needed
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    public function buildFormatter(): FormatterInterface;
}
