<?php declare(strict_types = 1);

namespace Everon\Logger\Contract\Plugin;

use Everon\Logger\Contract\Configurator\PluginConfiguratorInterface;

interface PluginFactoryInterface
{

    /**
     * Specification:
     * - Create plugin using custom logic or external dependencies
     * - Return logger plugin instance
     */
    public function create(PluginConfiguratorInterface $configurator): LoggerPluginInterface;

}
