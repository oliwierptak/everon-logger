# EveronLogger

[![Build and run tests](https://github.com/oliwierptak/everon-logger/actions/workflows/main.yml/badge.svg)](https://github.com/oliwierptak/everon-logger/actions/workflows/main.yml)

Monolog based, PSR-3 compliant logger, with pluggable architecture and simple configuration.

## Features

- Pluggable architecture, semantically versioned
- Simple setup, with autocompletion
- One unified configuration schema
- Plugins can be grouped into sets to easily create customized and very specific loggers instances
- Monolog's handlers and processors constructors details and dependencies are never exposed
- Based on [Monolog v3.x](https://github.com/Seldaek/monolog)

#### Simple Usage

Log everything at level `info` and above to `/tmp/example.log`.

```php
$streamPluginConfigurator = (new StreamLoggerPluginConfigurator)
    ->setLogLevel('info')
    ->setStreamLocation('/tmp/example.log');

$configurator = (new LoggerConfigurator)
    ->add($streamPluginConfigurator);

$logger = (new EveronLoggerFacade)->buildLogger($configurator);

$logger->info('lorem ipsum');
```

Content of `/tmp/example.log`.

```
[2020-11-15T16:29:16.400318+00:00] everon-logger.INFO: lorem ipsum [] []
```

## Configuration

The configuration is done by [simple data structures](https://github.com/oliwierptak/popo/) called `configurators`.
Each plugin configurator has its plugin specific settings.

For example, to use syslog and file logging, setup the `StreamLoggerPluginConfigurator`
and `SyslogLoggerPluginConfigurator`.

```php
$configurator = (new LoggerConfigurator)
    ->add(
        (new StreamLoggerPluginConfigurator)
            ->setLogLevel('debug')
            ->setStreamLocation('/tmp/example.log')
    )->add(
        (new SyslogLoggerPluginConfigurator)
            ->setLogLevel('info')
            ->setIdent('everon-logger-ident'));
```  

### Logger Handler / Plugin

A logger plugin is used to create and configure corresponding Monolog's handler.

Besides `LoggerPluginInterface` a plugin can also implement `PluginFormatterInterface`,
in which case the custom formatter provided by the plugin will be used.

### Setup with LoggerConfigurator

To set up a plugin with given handler, add it to the collection in `LoggerConfigurator` with `add()`.

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
    ->add($redisPluginConfigurator)
    ->addProcessor(MemoryUsageProcessor::class);

$logger = (new EveronLoggerFacade)->buildLogger($configurator);

$logger->info('lorem ipsum');
```

Content of `redis-queue-test` in redis.

```
[2020-11-15T16:39:12.495319+00:00] everon-logger.INFO: lorem ipsum [] {"memory_usage":"6 MB"}
```

## Logger processors

Add required processor classes to logger configurator with `addProcessor()`.

```php
$configurator = (new LoggerConfigurator)
    ->addProcessor(MemoryUsageProcessor::class)
    ->addProcessor(HostnameProcessor::class)
    ->addProcessor(...)
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

- PHP v8.1.x
- [Monolog v3.x](https://github.com/Seldaek/monolog)

## Installation

```
composer require everon/logger
```

_Note:_ You only need to install this package if you want to develop a plugin for `EveronLogger`.
Otherwise, install specific plugins. See above.
