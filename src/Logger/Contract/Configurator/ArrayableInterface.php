<?php

namespace Everon\Logger\Contract\Configurator;

interface ArrayableInterface
{
    /**
     * Specification:
     * - Reset state to defaults
     * - Load state from $data, merge with default values
     * - Instantiate any configurator defied as property with default values
     * - Return instance of updated configurator
     *
     * @param array $data
     *
     * @return \Everon\Logger\Contract\Configurator\ArrayableInterface
     */
    public function fromArray(array $data): ArrayableInterface;

    /**
     * Specification:
     * - Convert any property containing a configurator to an array as well
     * - Return (nested) array
     *
     * @return array
     */
    public function toArray(): array;
}
