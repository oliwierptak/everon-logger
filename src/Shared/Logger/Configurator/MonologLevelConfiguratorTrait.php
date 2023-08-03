<?php declare(strict_types = 1);

namespace Everon\Shared\Logger\Configurator;

use Monolog\Level;
use UnexpectedValueException;

trait MonologLevelConfiguratorTrait
{
    /**
     * The minimum logging level at which this handler will be triggered
     */
    protected Level $logLevel;

    public function getLogLevel(): Level
    {
        if (!isset($this->logLevel)) {
            $this->logLevel = Level::Debug;
        }

        return $this->logLevel;
    }

    public function setLogLevel(Level $logLevel): self
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    public function requireLogLevel(): Level
    {
        if (!isset($this->logLevel)) {
            throw new UnexpectedValueException('Required value of "logLevel" has not been set');
        }

        return $this->logLevel;
    }

    public function hasLogLevel(): bool
    {
        return isset($this->logLevel);
    }
}
