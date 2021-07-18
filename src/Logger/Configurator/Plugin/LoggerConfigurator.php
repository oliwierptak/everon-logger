<?php

/**
 * Everon logger configuration file. Auto-generated.
 */

declare(strict_types=1);

namespace Everon\Logger\Configurator\Plugin;

use UnexpectedValueException;

class LoggerConfigurator extends \Everon\Logger\Configurator\AbstractLoggerConfigurator implements \Everon\Logger\Contract\Configurator\LoggerConfiguratorInterface
{
    protected const SHAPE_PROPERTIES = [
        'name' => 'null|string',
        'processorClassCollection' => 'array',
        'timezone' => 'null|string',
    ];

    protected const METADATA = [
        'name' => ['type' => 'string', 'default' => 'everon-logger'],
        'processorClassCollection' => ['type' => 'array', 'default' => null],
        'timezone' => ['type' => 'string', 'default' => 'UTC'],
    ];

    protected ?string $name = 'everon-logger';

    /** Monolog processor's collection */
    protected array $processorClassCollection = [];

    /** Logger's timezone */
    protected ?string $timezone = 'UTC';
    protected array $updateMap = [];

    public function setName(?string $name): self
    {
        $this->name = $name; $this->updateMap['name'] = true; return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function requireName(): string
    {
        if (static::METADATA['name']['type'] === 'popo' && $this->name === null) {
            $popo = static::METADATA['name']['default'];
            $this->name = new $popo;
        }

        if ($this->name === null) {
            throw new UnexpectedValueException('Required value of "name" has not been set');
        }
        return $this->name;
    }

    public function hasName(): bool
    {
        return $this->name !== null || ($this->name !== null && array_key_exists('name', $this->updateMap));
    }

    /**
     * Monolog processor's collection
     */
    public function setProcessorClassCollection(array $processorClassCollection): self
    {
        $this->processorClassCollection = $processorClassCollection; $this->updateMap['processorClassCollection'] = true; return $this;
    }

    /**
     * @return string[]
     */
    public function getProcessorClassCollection(): array
    {
        return $this->processorClassCollection;
    }

    /**
     * Monolog processor's collection
     */
    public function requireProcessorClassCollection(): array
    {
        if (static::METADATA['processorClassCollection']['type'] === 'popo' && $this->processorClassCollection === null) {
            $popo = static::METADATA['processorClassCollection']['default'];
            $this->processorClassCollection = new $popo;
        }

        if (empty($this->processorClassCollection)) {
            throw new UnexpectedValueException('Required value of "processorClassCollection" has not been set');
        }
        return $this->processorClassCollection;
    }

    public function hasProcessorClassCollection(): bool
    {
        return !empty($this->processorClassCollection) && array_key_exists('processorClassCollection', $this->updateMap);
    }

    public function addProcessorClass(string $item): self
    {
        $this->processorClassCollection[] = $item;

        $this->updateMap['processorClassCollection'] = true;

        return $this;
    }

    /**
     * Logger's timezone
     */
    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone; $this->updateMap['timezone'] = true; return $this;
    }

    /**
     * Logger's timezone
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * Logger's timezone
     */
    public function requireTimezone(): string
    {
        if (static::METADATA['timezone']['type'] === 'popo' && $this->timezone === null) {
            $popo = static::METADATA['timezone']['default'];
            $this->timezone = new $popo;
        }

        if ($this->timezone === null) {
            throw new UnexpectedValueException('Required value of "timezone" has not been set');
        }
        return $this->timezone;
    }

    public function hasTimezone(): bool
    {
        return $this->timezone !== null || ($this->timezone !== null && array_key_exists('timezone', $this->updateMap));
    }

    #[\JetBrains\PhpStorm\ArrayShape(self::SHAPE_PROPERTIES)]
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'processorClassCollection' => $this->processorClassCollection,
            'timezone' => $this->timezone,
        ];

        array_walk(
            $data,
            function (&$value, $name) use ($data) {
                $popo = static::METADATA[$name]['default'];
                if (static::METADATA[$name]['type'] === 'popo') {
                    $value = $this->$name !== null ? $this->$name->toArray() : (new $popo)->toArray();
                }
            }
        );

        return $data;
    }

    public function fromArray(#[\JetBrains\PhpStorm\ArrayShape(self::SHAPE_PROPERTIES)] array $data): self
    {
        foreach (static::METADATA as $name => $meta) {
            $value = $data[$name] ?? $this->$name ?? null;
            $popoValue = $meta['default'];

            if ($popoValue !== null && $meta['type'] === 'popo') {
                $popo = new $popoValue;

                if (is_array($value)) {
                    $popo->fromArray($value);
                }

                $value = $popo;
            }

            $this->$name = $value;
            $this->updateMap[$name] = true;
        }

        return $this;
    }

    public function isNew(): bool
    {
        return empty($this->updateMap) === true;
    }

    public function requireAll(): self
    {
        $errors = [];

        try {
            $this->requireName();
        }
        catch (\Throwable $throwable) {
            $errors['name'] = $throwable->getMessage();
        }
        try {
            $this->requireProcessorClassCollection();
        }
        catch (\Throwable $throwable) {
            $errors['processorClassCollection'] = $throwable->getMessage();
        }
        try {
            $this->requireTimezone();
        }
        catch (\Throwable $throwable) {
            $errors['timezone'] = $throwable->getMessage();
        }

        if (empty($errors) === false) {
            throw new UnexpectedValueException(
                implode("\n", $errors)
            );
        }

        return $this;
    }
}
