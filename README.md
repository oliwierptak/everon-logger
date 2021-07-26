# EveronLogger

PSR-3 compliant logger, with pluggable architecture and simple configuration.

## Features


- Pluggable architecture, semantically versioned
- Simple data structure based configuration
- One unified plugin configuration schema
- Logger handlers and processors created and configured via plugins
- Using configurators, plugins can be grouped into sets to easily create customized and very specific loggers instances
- Based on [Monolog v2.x](https://github.com/Seldaek/monolog)


#### Simple Usage

Log everything at level `info` and above to `/tmp/example.log`.

```php
$streamPluginConfigurator = (new StreamLoggerPluginConfigurator)
    ->setLogLevel('info')
    ->setStreamLocation('/tmp/example.log');

$configurator = (new LoggerConfigurator)
    ->addPluginConfigurator($streamPluginConfigurator);

$logger = (new EveronLoggerFacade)->buildLogger($configurator);

$logger->info('lorem ipsum');
```

Content of `/tmp/example.log`.
```
[2020-11-15T16:29:16.400318+00:00] everon-logger.INFO: lorem ipsum [] []
```

## Configuration

The configuration is done by [simple data classes](https://github.com/oliwierptak/popo/) called `configurators`.
Each plugin configurator has only plugin specific settings.

For example, setup syslog and file logging.

```php
$configurator = (new LoggerConfigurator)
    ->addPluginConfigurator(
        (new StreamLoggerPluginConfigurator)
            ->setLogLevel('debug')
            ->setStreamLocation('/tmp/example.log')
    )->addPluginConfigurator(
        (new SyslogLoggerPluginConfigurator)
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident'));
```  

### Logger Handler / Plugin

A logger plugin is used to create and configure corresponding monolog handler.

Besides `LoggerPluginInterface` a plugin can also implement `PluginFormatterInterface`,
in which case the custom formatter provided by the plugin will be used.


### Setup with LoggerConfigurator

To setup a plugin with given handler, add it to the collection in `LoggerConfigurator` with `addPluginConfigurator()`.

For example, setup logging to a redis server and enable memory usage processor.

```php
$redisPluginConfigurator = new RedisLoggerPluginConfigurator;
$redisPluginConfigurator
    ->setLogLevel('info')
    ->setKey('redis-queue-test')
    ->requireRedisConnection()
        ->setHost('redis.host')
        ->setTimeout(10);

$configurator = (new LoggerConfigurator)
    ->setName('everon-logger-example')
    ->addProcessorClass(MemoryUsageProcessor::class)
    ->addPluginConfigurator($redisPluginConfigurator);

$logger = (new EveronLoggerFacade)->buildLogger($configurator);

$logger->info('lorem ipsum');
```

Content of `redis-queue-test` in redis.
```
[2020-11-15T16:39:12.495319+00:00] everon-logger.INFO: lorem ipsum [] {"memory_usage":"6 MB"}
```

## Logger processors

Add required processor classes to logger configurator with `addProcessorClass()`.

```php
$configurator = (new LoggerConfigurator)
    ->addProcessorClass(MemoryUsageProcessor::class)
    ->addProcessorClass(HostnameProcessor::class)
    ->addProcessorClass(...)
    ...
```

## Plugins

### Basic

Set of plugins that require no extra vendor dependencies.

```
composer require everon/logger-basic
```

[Repository](https://github.com/oliwierptak/everon-logger-basic)


### Gelf

Set of plugins for Graylog2 handlers.

```
composer require everon/logger-gelf
```

[Repository](https://github.com/oliwierptak/everon-logger-gelf)


### Redis

Set of plugins for Redis handler.

[Repository](https://github.com/oliwierptak/everon-logger-redis)

```
composer require everon/logger-redis
```

## Requirements

- PHP v8.x
- [Monolog v2.x](https://github.com/Seldaek/monolog)

_Note_: Use v3.x for compatibility with PHP v7.4.


## Installation

```
composer require everon/logger
```

_Note:_ You only need to install this package if you want to develop a plugin for `EveronLogger`.
Otherwise, install specific plugins. See above.
