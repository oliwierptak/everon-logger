<?php

declare(strict_types = 1);

namespace Everon\Logger;

use Everon\Logger\Configurator\Plugin\LoggerConfigurator;
use Psr\Log\LoggerInterface;

class EveronLoggerFacade implements EveronLoggerFacadeInterface
{
    protected EveronLoggerFactory $factory;

    public function buildLogger(LoggerConfigurator $configurator): LoggerInterface
    {
        return $this->getFactory()
            ->createBuilderFromConfigurator($configurator)
            ->buildLogger();
    }

    protected function getFactory(): EveronLoggerFactory
    {
        if (empty($this->factory)) {
            $this->factory = new EveronLoggerFactory();
        }

        return $this->factory;
    }

    public function setFactory(EveronLoggerFactory $factory): void
    {
        $this->factory = $factory;
    }
}
