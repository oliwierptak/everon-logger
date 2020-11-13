<?php

declare(strict_types = 1);

namespace Everon\Logger\Contract\Container;

interface LoggerContainerInterface
{
    /**
     * Specification:
     * - Create set of plugins implementing \Everon\Logger\Contract\Plugin\LoggerPluginInterface
     *
     * @return \Everon\Logger\Contract\Plugin\LoggerPluginInterface[]
     */
    public function createPluginCollection(): array;
}
