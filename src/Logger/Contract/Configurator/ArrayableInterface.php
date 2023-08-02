<?php declare(strict_types = 1);

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
     * @param array<string, mixed> $data
     */

    public function fromArray(array $data): self;

    /**
     * Specification:
     * - Convert any property containing a configurator to an array as well
     * - Return (nested) array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

}
