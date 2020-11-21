# EveronLogger

PSR-3 compliant logger, with pluggable architecture and simple configuration.
 
## Features


 - Pluggable architecture, contracts, semantically versioned
 - One unified plugin schema
 - Simple value object based configuration 
 - Logger handlers and processors created and configured via plugins
 - Plugins can be grouped into sets to easily create customized and very specific loggers instances
 - Based on Monolog 
 
 
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

The configuration is done by simple value objects called `configurators`.
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
            ->setIdent('everon-logger-ident')
    );
```  

## Plugins

A logger plugin is used to create and configure corresponding monolog handler.

Besides `LoggerPluginInterface` a plugin can also implement `PluginFormatterInterface`,
in which case the custom formatter provided by the plugin will be used.


### Plugin setup

To setup a plugin add it to the collection in `LoggerConfigurator` with `addPluginConfigurator()`.
  
For example, setup logging to a redis server and enable memory usage processor.

```php
$configurator = (new LoggerConfigurator)
    ->setName('everon-logger-example')
    ->addProcessorClass(MemoryUsageProcessor::class)
    ->addPluginConfigurator(
        (new RedisLoggerConfigurator)
            ->setLogLevel('info')
            ->setKey('redis-queue-test')
            ->getRedisConnection()
                ->setHost('redis.host')
                ->setTimeout(10)
    );

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
    ->addPluginConfigurator(
    ...
```

## Installation

```
composer require everon/logger
```
