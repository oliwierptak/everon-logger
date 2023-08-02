<?php

namespace Everon\Logger\Contract\Configurator;

interface PluginConfiguratorInterface extends ArrayableInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function fromArray(array $data): PluginConfiguratorInterface;

    /**
     * Specification:
     * - Check value and throw exception if value is null.
     * - Return plugin's classname
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requirePluginClass(): string;


    /**
     * Specification:
     * - A plugin factory class must implement Everon\Logger\Contract\Plugin\PluginFactoryInterface
     * - Return plugin's factory class name. This class will be used to instantiate the plugin.
     *
     * @return string|null
     */
    public function getPluginFactoryClass(): ?string;

    /**
     * Specification:
     * - Check value and throw exception if value is null.
     * - Return the minimum logging level at which this handler will be triggered
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requireLogLevel(): string;

    /**
     * Specification:
     * - Whether the messages that are handled can bubble up the stack or not
     *
     * @return bool|null
     */
    public function shouldBubble(): ?bool;
}
