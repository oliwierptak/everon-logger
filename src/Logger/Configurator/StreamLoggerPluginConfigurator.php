<?php declare(strict_types = 1);

namespace Everon\Logger\Configurator;

use InvalidArgumentException;
use UnexpectedValueException;
use function array_key_exists;
use function ctype_upper;
use function is_array;
use function is_object;
use function method_exists;
use function sprintf;
use function strtolower;
use function trim;

/**
 * Code generated by POPO generator, do not edit.
 * https://packagist.org/packages/popo/generator
 */
class StreamLoggerPluginConfigurator 
{
    protected array $data = array (
  'streamLocation' => NULL,
  'shouldBubble' => true,
  'filePermission' => NULL,
  'useLocking' => false,
);

    protected array $default = array (
  'streamLocation' => NULL,
  'shouldBubble' => true,
  'filePermission' => NULL,
  'useLocking' => false,
);

    protected array $propertyMapping = array (
  'streamLocation' => 'string',
  'shouldBubble' => 'bool',
  'filePermission' => 'int',
  'useLocking' => 'bool',
);

    protected array $collectionItems = array (
);

    protected array $updateMap = [];

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    protected function popoGetValue(string $property)
    {
        if (!isset($this->data[$property])) {
            if ($this->typeIsObject($this->propertyMapping[$property])) {
                $popo = new $this->propertyMapping[$property];
                $this->data[$property] = $popo;
            } else {
                return null;
            }
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return void
     */
    protected function popoSetValue(string $property, $value): void
    {
        $this->data[$property] = $value;

        $this->updateMap[$property] = true;
    }

    /**
     * @param string $property
     *
     * @throws UnexpectedValueException
     * @return void
     */
    protected function assertPropertyValue(string $property): void
    {
        if (!isset($this->data[$property])) {
            throw new UnexpectedValueException(sprintf(
                'Required value of "%s" has not been set',
                $property
            ));
        }
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function addCollectionItem(string $propertyName, $value): void
    {
        $type = trim(strtolower($this->propertyMapping[$propertyName]));
        $collection = $this->popoGetValue($propertyName) ?? [];

        if (!is_array($collection) || $type !== 'array') {
            throw new InvalidArgumentException('Cannot add item to non array type: ' . $propertyName);
        }

        $collection[] = $value;

        $this->popoSetValue($propertyName, $collection);
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->propertyMapping as $key => $type) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = $this->default[$key] ?? null;
            }
            $value = $this->data[$key];

            if ($this->isCollectionItem($key) && is_array($value)) {
                foreach ($value as $popo) {
                    if (is_object($popo) && method_exists($popo, 'toArray')) {
                        $data[$key][] = $popo->toArray();
                    }
                }

                continue;
            }

            if (is_object($value) && method_exists($value, 'toArray')) {
                $data[$key] = $value->toArray();
                continue;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    public function fromArray(array $data): StreamLoggerPluginConfigurator
    {
        foreach ($this->propertyMapping as $key => $type) {
            $result[$key] = $this->default[$key] ?? null;

            if ($this->typeIsObject($type)) {
                $popo = new $this->propertyMapping[$key];
                if (method_exists($popo, 'fromArray')) {
                    $popoData = $data[$key] ?? $this->default[$key] ?? [];
                    $popo->fromArray($popoData);
                }
                $result[$key] = $popo;

                continue;
            }

            if (array_key_exists($key, $data)) {
                if ($this->isCollectionItem($key)) {
                    foreach ($data[$key] as $popoData) {
                        $popo = new $this->collectionItems[$key]();
                        if (method_exists($popo, 'fromArray')) {
                            $popo->fromArray($popoData);
                        }
                        $result[$key][] = $popo;
                    }
                } else {
                    $result[$key] = $data[$key];
                }
            }
        }

        $this->data = $result;

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $result)) {
                continue;
            }

            $type = $this->propertyMapping[$key] ?? null;
            if ($type !== null) {
                $value = $this->typecastValue($type, $result[$key]);
                $this->popoSetValue($key, $value);
            }
        }

        return $this;
    }

    /**
     * @param string $type
     * @param mixed $value
     *
     * @return mixed
     */
    protected function typecastValue(string $type, $value)
    {
        if ($value === null) {
            return $value;
        }

        switch ($type) {
            case 'int':
                $value = (int)$value;
                break;
            case 'string':
                $value = (string)$value;
                break;
            case 'bool':
                $value = (bool)$value;
                break;
            case 'array':
                $value = (array)$value;
                break;
        }

        return $value;
    }

    protected function isCollectionItem(string $key): bool
    {
        return array_key_exists($key, $this->collectionItems);
    }

    protected function typeIsObject(string $value): bool
    {
        return $value[0] === '\\' && ctype_upper($value[1]);
    }
    
    /**
     * @return string|null
     */
    public function getStreamLocation(): ?string
    {
        return $this->popoGetValue('streamLocation');
    }

    /**
     * @param string|null $streamLocation
     *
     * @return StreamLoggerPluginConfigurator
     */
    public function setStreamLocation(?string $streamLocation): StreamLoggerPluginConfigurator
    {
        $this->popoSetValue('streamLocation', $streamLocation);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requireStreamLocation(): string
    {
        $this->assertPropertyValue('streamLocation');

        return (string)$this->popoGetValue('streamLocation');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasStreamLocation(): bool
    {
        return $this->updateMap['streamLocation'] ?? false;
    }

    /**
     * @return boolean|null
     */
    public function shouldBubble(): ?bool
    {
        return $this->popoGetValue('shouldBubble');
    }

    /**
     * @param boolean|null $shouldBubble
     *
     * @return StreamLoggerPluginConfigurator
     */
    public function setShouldBubble(?bool $shouldBubble): StreamLoggerPluginConfigurator
    {
        $this->popoSetValue('shouldBubble', $shouldBubble);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return boolean
     */
    public function requireShouldBubble(): bool
    {
        $this->assertPropertyValue('shouldBubble');

        return (bool)$this->popoGetValue('shouldBubble');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasShouldBubble(): bool
    {
        return $this->updateMap['shouldBubble'] ?? false;
    }

    /**
     * @return integer|null
     */
    public function getFilePermission(): ?int
    {
        return $this->popoGetValue('filePermission');
    }

    /**
     * @param integer|null $filePermission
     *
     * @return StreamLoggerPluginConfigurator
     */
    public function setFilePermission(?int $filePermission): StreamLoggerPluginConfigurator
    {
        $this->popoSetValue('filePermission', $filePermission);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return integer
     */
    public function requireFilePermission(): int
    {
        $this->assertPropertyValue('filePermission');

        return (int)$this->popoGetValue('filePermission');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasFilePermission(): bool
    {
        return $this->updateMap['filePermission'] ?? false;
    }

    /**
     * @return boolean|null
     */
    public function useLocking(): ?bool
    {
        return $this->popoGetValue('useLocking');
    }

    /**
     * @param boolean|null $useLocking
     *
     * @return StreamLoggerPluginConfigurator
     */
    public function setUseLocking(?bool $useLocking): StreamLoggerPluginConfigurator
    {
        $this->popoSetValue('useLocking', $useLocking);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return boolean
     */
    public function requireUseLocking(): bool
    {
        $this->assertPropertyValue('useLocking');

        return (bool)$this->popoGetValue('useLocking');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasUseLocking(): bool
    {
        return $this->updateMap['useLocking'] ?? false;
    }

    
}
