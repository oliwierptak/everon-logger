<?php declare(strict_types = 1);

namespace Everon\Logger\Configurator\Plugin;

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
class ErrorLogLoggerPluginConfigurator extends \Everon\Logger\Configurator\AbstractPluginConfigurator 
{
    protected array $data = array (
  'pluginClass' => \Everon\Logger\Plugin\Redis\RedisLoggerPlugin::class,
  'pluginFactoryClass' => NULL,
  'logLevel' => 'debug',
  'shouldBubble' => true,
  'messageType' => \Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM,
  'expandNewlines' => false,
);

    protected array $default = array (
  'pluginClass' => \Everon\Logger\Plugin\Redis\RedisLoggerPlugin::class,
  'pluginFactoryClass' => NULL,
  'logLevel' => 'debug',
  'shouldBubble' => true,
  'messageType' => \Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM,
  'expandNewlines' => false,
);

    protected array $propertyMapping = array (
  'pluginClass' => 'string',
  'pluginFactoryClass' => 'string',
  'logLevel' => 'string',
  'shouldBubble' => 'bool',
  'messageType' => 'int',
  'expandNewlines' => 'bool',
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

    public function fromArray(array $data): ErrorLogLoggerPluginConfigurator
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
    public function getPluginClass(): ?string
    {
        return $this->popoGetValue('pluginClass');
    }

    /**
     * @param string|null $pluginClass
     *
     * @return ErrorLogLoggerPluginConfigurator
     */
    public function setPluginClass(?string $pluginClass): ErrorLogLoggerPluginConfigurator
    {
        $this->popoSetValue('pluginClass', $pluginClass);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requirePluginClass(): string
    {
        $this->assertPropertyValue('pluginClass');

        return (string)$this->popoGetValue('pluginClass');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasPluginClass(): bool
    {
        return $this->updateMap['pluginClass'] ?? false;
    }

    /**
     * @return string|null Defines custom plugin factory to be used to create a plugin
     */
    public function getPluginFactoryClass(): ?string
    {
        return $this->popoGetValue('pluginFactoryClass');
    }

    /**
     * @param string|null $pluginFactoryClass Defines custom plugin factory to be used to create a plugin
     *
     * @return ErrorLogLoggerPluginConfigurator
     */
    public function setPluginFactoryClass(?string $pluginFactoryClass): ErrorLogLoggerPluginConfigurator
    {
        $this->popoSetValue('pluginFactoryClass', $pluginFactoryClass);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string Defines custom plugin factory to be used to create a plugin
     */
    public function requirePluginFactoryClass(): string
    {
        $this->assertPropertyValue('pluginFactoryClass');

        return (string)$this->popoGetValue('pluginFactoryClass');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasPluginFactoryClass(): bool
    {
        return $this->updateMap['pluginFactoryClass'] ?? false;
    }

    /**
     * @return string|null The minimum logging level at which this handler will be triggered
     */
    public function getLogLevel(): ?string
    {
        return $this->popoGetValue('logLevel');
    }

    /**
     * @param string|null $logLevel The minimum logging level at which this handler will be triggered
     *
     * @return ErrorLogLoggerPluginConfigurator
     */
    public function setLogLevel(?string $logLevel): ErrorLogLoggerPluginConfigurator
    {
        $this->popoSetValue('logLevel', $logLevel);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string The minimum logging level at which this handler will be triggered
     */
    public function requireLogLevel(): string
    {
        $this->assertPropertyValue('logLevel');

        return (string)$this->popoGetValue('logLevel');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasLogLevel(): bool
    {
        return $this->updateMap['logLevel'] ?? false;
    }

    /**
     * @return boolean|null Whether the messages that are handled can bubble up the stack or not
     */
    public function shouldBubble(): ?bool
    {
        return $this->popoGetValue('shouldBubble');
    }

    /**
     * @param boolean|null $shouldBubble Whether the messages that are handled can bubble up the stack or not
     *
     * @return ErrorLogLoggerPluginConfigurator
     */
    public function setShouldBubble(?bool $shouldBubble): ErrorLogLoggerPluginConfigurator
    {
        $this->popoSetValue('shouldBubble', $shouldBubble);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return boolean Whether the messages that are handled can bubble up the stack or not
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
     * @return integer|null Says where the error should go.
     */
    public function getMessageType(): ?int
    {
        return $this->popoGetValue('messageType');
    }

    /**
     * @param integer|null $messageType Says where the error should go.
     *
     * @return ErrorLogLoggerPluginConfigurator
     */
    public function setMessageType(?int $messageType): ErrorLogLoggerPluginConfigurator
    {
        $this->popoSetValue('messageType', $messageType);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return integer Says where the error should go.
     */
    public function requireMessageType(): int
    {
        $this->assertPropertyValue('messageType');

        return (int)$this->popoGetValue('messageType');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasMessageType(): bool
    {
        return $this->updateMap['messageType'] ?? false;
    }

    /**
     * @return boolean|null If set to true, newlines in the message will be expanded to be take multiple log entries
     */
    public function expandNewlines(): ?bool
    {
        return $this->popoGetValue('expandNewlines');
    }

    /**
     * @param boolean|null $expandNewlines If set to true, newlines in the message will be expanded to be take multiple log entries
     *
     * @return ErrorLogLoggerPluginConfigurator
     */
    public function setExpandNewlines(?bool $expandNewlines): ErrorLogLoggerPluginConfigurator
    {
        $this->popoSetValue('expandNewlines', $expandNewlines);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return boolean If set to true, newlines in the message will be expanded to be take multiple log entries
     */
    public function requireExpandNewlines(): bool
    {
        $this->assertPropertyValue('expandNewlines');

        return (bool)$this->popoGetValue('expandNewlines');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasExpandNewlines(): bool
    {
        return $this->updateMap['expandNewlines'] ?? false;
    }

    
}