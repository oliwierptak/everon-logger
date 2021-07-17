<?php

declare(strict_types = 1);

namespace Everon\Logger\Configurator;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;

abstract class AbstractPluginConfigurator implements PluginConfiguratorInterface
{
    /**
     * Defines custom plugin factory to be used to create a plugin
     */
    protected ?string $pluginFactoryClass = null;
    /**
     * The minimum logging level at which this handler will be triggered
     */
    protected string $logLevel = 'debug';
    /**
     * Whether the messages that are handled can bubble up the stack or not
     */
    protected bool $shouldBubble = true;

    public function getPluginFactoryClass(): ?string
    {
        return $this->pluginFactoryClass;
    }

    public function setPluginFactoryClass(?string $pluginFactoryClass): self
    {
        $this->pluginFactoryClass = $pluginFactoryClass;

        return $this;
    }

    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    public function setLogLevel(string $logLevel): self
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    public function requireLogLevel(): string
    {
        if (trim($this->logLevel) === '') {
            throw new \UnexpectedValueException('Required value of "logLevel" has not been set');
        }

        return $this->logLevel;
    }

    public function shouldBubble(): bool
    {
        return $this->shouldBubble;
    }

    public function setShouldBubble(bool $shouldBubble): self
    {
        $this->shouldBubble = $shouldBubble;

        return $this;
    }
}
