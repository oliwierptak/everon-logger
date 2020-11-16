<?php

namespace Everon\Logger\Contract\Plugin;

interface PluginConfiguratorResolverInterface
{
    /**
     * Specification:
     * - Resolve configurator for specific plugin, stored in LoggerPluginConfigurator
     * - Return configurator for specific plugin
     *
     * @return mixed
     */
    public function resolveConfigurator();
}
