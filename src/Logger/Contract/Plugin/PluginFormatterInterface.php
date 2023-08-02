<?php declare(strict_types = 1);

namespace Everon\Logger\Contract\Plugin;

use Monolog\Formatter\FormatterInterface;

interface PluginFormatterInterface
{

    /**
     * Specification:
     * - Create instance of formatter implementing Monolog\Formatter\FormatterInterface
     * - Return formatter instance or null in case custom formatter is not needed
     */
    public function buildFormatter(): FormatterInterface;

}
