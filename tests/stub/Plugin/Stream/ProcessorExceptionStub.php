<?php

declare(strict_types = 1);

namespace EveronLoggerTests\Stub\Plugin\Stream;

use InvalidArgumentException;
use Monolog\Processor\ProcessorInterface;

class ProcessorExceptionStub implements ProcessorInterface
{
    public function __construct()
    {
        throw new InvalidArgumentException('Invalid value for foo bar');
    }

    public function __invoke(array $record)
    {
        throw new InvalidArgumentException('Invalid value for foo bar');
    }
}
