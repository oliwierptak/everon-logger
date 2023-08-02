<?php declare(strict_types = 1);

namespace Everon\Logger\Builder;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;
use Everon\Logger\Contract\Plugin\LoggerPluginInterface;
use Everon\Logger\Exception\PluginBuildException;
use Throwable;
use function assert;
use function sprintf;

class PluginBuilder
{

    /** @throws \Everon\Logger\Exception\PluginBuildException */
    public function buildPlugin(PluginConfiguratorInterface $pluginConfigurator): LoggerPluginInterface
    {
        try {
            $pluginFactoryClass = $pluginConfigurator->getPluginFactoryClass();

            if ($pluginFactoryClass !== null) {
                assert($pluginFactoryClass instanceof \Everon\Logger\Contract\Plugin\PluginFactoryInterface);
                return (new $pluginFactoryClass)->create($pluginConfigurator);
            }

            /* @phpstan-ignore-next-line */
            $pluginClassName = $pluginConfigurator->requirePluginClass();

            return new $pluginClassName($pluginConfigurator);
        }
        catch (Throwable $exception) {
            throw new PluginBuildException(
                sprintf(
                    'Could not build plugin: "%s". Error: %s',
                    $pluginConfigurator->requirePluginClass(),
                    $exception->getMessage(),
                ), $exception->getCode(), $exception
            );
        }
    }

}
