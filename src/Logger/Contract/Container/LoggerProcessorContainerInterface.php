<?php

declare(strict_types = 1);

namespace Everon\Logger\Contract\Container;

interface LoggerProcessorContainerInterface
{
    /**
     * Specification:
     * - Creates set of processors implementing Monolog\Processor\ProcessorInterface
     *
     * @return \Monolog\Processor\ProcessorInterface[]
     */
    public function createProcessorCollection(): array;
}
