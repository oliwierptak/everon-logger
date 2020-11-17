<?php

declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Contract\Plugin\PluginFormatterInterface;
use Everon\Logger\Exception\HandlerBuildException;
use Monolog\Handler\HandlerInterface;
use Throwable;

class HandlerBuilder
{
    /**
     * @param \Everon\Logger\Contract\Plugin\LoggerPluginInterface $plugin
     *
     * @return \Monolog\Handler\HandlerInterface
     * @throws \Everon\Logger\Exception\HandlerBuildException
     */
    public function buildHandler(LoggerPluginInterface $plugin): HandlerInterface
    {
        try {
            $handler = $plugin->buildHandler();
            if ($plugin instanceof PluginFormatterInterface) {
                $formatter = $plugin->buildFormatter();
                $handler->setFormatter($formatter);
            }

            return $handler;
        }
        catch (Throwable $exception) {
            throw new HandlerBuildException(sprintf(
                'Could not build handler in plugin: "%s". Error: %s',
                get_class($plugin),
                $exception->getMessage()
            ), $exception->getCode(), $exception);
        }
    }
}
