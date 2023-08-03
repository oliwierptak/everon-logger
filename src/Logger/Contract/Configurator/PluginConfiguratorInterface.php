<?php declare(strict_types = 1);

namespace Everon\Logger\Contract\Configurator;

use Monolog\Level;

interface PluginConfiguratorInterface extends ArrayableInterface
{

    /** @param array<string, mixed> $data */
    public function fromArray(array $data): self;

    /**
     * Specification:
     * - Check value and throw exception if value is null.
     * - Return plugin's classname
     *
     * @throws \UnexpectedValueException
     */
    public function requirePluginClass(): string;

    /**
     * Specification:
     * - A plugin factory class must implement Everon\Logger\Contract\Plugin\PluginFactoryInterface
     * - Return plugin's factory class name. This class will be used to instantiate the plugin.
     */
    public function getPluginFactoryClass(): ?string;

    /**
     * Specification:
     * - Check value and throw exception if value is null.
     * - Return the minimum logging level at which this handler will be triggered
     *
     * @throws \UnexpectedValueException
     */
    public function requireLogLevel(): Level;

    /**
     * Specification:
     * - Whether the messages that are handled can bubble up the stack or not
     */
    public function shouldBubble(): ?bool;

}
