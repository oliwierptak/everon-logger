<?php declare(strict_types = 1);

namespace Everon\Shared\Logger\Configurator;

use Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface;
use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;
use InvalidArgumentException;
use function array_key_exists;

abstract class AbstractLoggerConfigurator implements LoggerConfiguratorInterface
{
    use MonologLevelConfiguratorTrait;

    /**
     * Validate builder's configuration and throw exception on error.
     * Disabled by default, canRun() should determine without throwing exception, if the handler can be built
     *
     * @var bool
     */
    protected bool $validateConfiguration = false;

    /**
     * @var \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[]
     */
    protected array $pluginConfiguratorCollection = [];

    /**
     * [pluginClassName => configurator]
     *
     * @return \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[]
     */
    public function getPluginConfiguratorCollection(): array
    {
        return $this->pluginConfiguratorCollection;
    }

    /**
     * [pluginClass => configurator]
     *
     * @param \Everon\Logger\Contract\Configurator\PluginConfiguratorInterface[] $pluginConfiguratorCollection
     *
     * @return \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
     */
    public function setPluginConfiguratorCollection(array $pluginConfiguratorCollection): LoggerConfiguratorInterface
    {
        $this->pluginConfiguratorCollection = $pluginConfiguratorCollection;

        return $this;
    }

    public function add(PluginConfiguratorInterface $pluginConfigurator): LoggerConfiguratorInterface
    {
        $this->pluginConfiguratorCollection[$pluginConfigurator->requirePluginClass()] = $pluginConfigurator;

        return $this;
    }

    public function setValidateConfiguration(bool $validateConfiguration): LoggerConfiguratorInterface
    {
        $this->validateConfiguration = $validateConfiguration;

        return $this;
    }

    public function validateConfiguration(): bool
    {
        return $this->validateConfiguration;
    }

    public function getConfiguratorByPluginName(string $pluginClass): PluginConfiguratorInterface
    {
        if (!array_key_exists($pluginClass, $this->pluginConfiguratorCollection)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Could not find plugin configurator for plugin: "%s"',
                    $pluginClass,
                ),
            );
        }

        return $this->pluginConfiguratorCollection[$pluginClass];
    }
}
