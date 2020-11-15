# EveronLogger

PSR-3 compliant logger, with pluggable architecture and simple configuration.
 
## Features


 - Pluggable architecture, contracts, semantically versioned
 - One unified plugin schema (JSON)
 - Simple value object based configuration (POPO) 
 - All logger handlers and processors are created and configured via plugins
 - Plugins can be grouped into sets to easily create customized and very specific loggers instances
 - Based on Monolog 
 
 
#### Simple Usage

Configure `StreamLoggerPlugin` to start logging everything at level `info` and above to `/tmp/example.log`.

```php
$configurator = (new LoggerPluginConfigurator())
    ->addPluginClass(StreamLoggerPlugin::class);

$configurator->getStreamConfigurator()
    ->setLogLevel('info')
    ->setStreamLocation('/tmp/example.log');

$logger = (new EveronLoggerFacade())->buildLogger($configurator);

$logger->info('lorem ipsum');
```

Content of `/tmp/example.log`. 
```
[2020-11-15T16:29:16.400318+00:00] everon-logger.INFO: lorem ipsum [] []
```
  
## Installation

```
composer require everon/logger
```

## Configuration
All configuration is done by simple value objects called `configurators`.
 
Each logger plugin has its own configurator accessible in the `LoggerPluginConfigurator`.

Each plugin configuration has only plugin specific settings.

For example: 

```php
$configurator = (new LoggerPluginConfigurator())
    ->setName('my-app-logger')
    ->getStreamLoggerConfigurator()
    ->setStreamLocation('/tmp/foo.log');
```  

## Plugins
A logger plugin is used to create and configure corresponding monolog handler.

Besides `LoggerPluginInterface` a plugin can also implement `LoggerPluginFormatterInterface`,
in which case the custom formatter provided by the plugin will be used.


## Simple Usage with Configurator

The required plugins and processors can be added to `LoggerPluginConfigurator`. 

To setup specific handler, use the plugin's configurator.
  
For example: setup logging to a file and enable memory usage processor.

```php
$configurator = (new LoggerPluginConfigurator())
    ->setName('everon-example')
    ->addPluginClass(StreamLoggerPlugin::class)
    ->addPluginProcessorClass(MemoryUsageProcessor::class);

$configurator
    ->getStreamConfigurator()
    ->setLogLevel('info')
    ->setStreamLocation('/tmp/example.log');

$logger = (new EveronLoggerFacade())->buildLogger($configurator);

$logger->info('lorem ipsum');
```

Content of `/tmp/example.log`.
```
[2020-11-15T16:39:12.495319+00:00] everon-logger.INFO: lorem ipsum [] {"memory_usage":"6 MB"}
```


## Advanced Usage with Plugin Container

Plugin Container can be used for cases where a handler, plugin or processor needs extra/external dependencies or custom logic.


Example plugin container with plugins setup for CLI applications. 

```php
class CliAppLoggerContainer implements LoggerContainerInterface
{
    public function createPluginCollection(): array
    {
        return [
            new SymfonyConsolePlugin($this->configurator->getSymfonyConsoleConfigurator()),  
            new SyslogLoggerPlugin($this->configurator->getSyslogConfigurator()),  
            // ...
        ];
    }
}
```

Example plugin container with plugins setup for WEB applications.

```php
class WebAppLoggerContainer implements LoggerContainerInterface
{
    public function createPluginCollection(): array
    {
        return [
            new GelfLoggerPlugin($this->configurator->getGelfConfigurator()),  
            new StreamLoggerPlugin($this->configurator->getStreamConfigurator()),  
            // ...
        ];
    }
}
```

Configure and build logger instances using containers above.

```php
$configurator = (new LoggerPluginConfigurator())
    ->setName('my-app-logger')
    ->getStreamConfigurator()
        ->setLogLevel('debug')
        ->setStreamLocation('/tmp/my-app-debug.log')
    ->getSyslogConfigurator()
        ->setLogLevel('info')
        ->setIdent('everon-logger-ident')
    //...
;

$loggerForCliApp = (new EveronLoggerFacade())->buildLoggerFromContainer(
    new CliAppLoggerContainer($configurator)
);

$loggerForWebApp = (new EveronLoggerFacade())->buildLoggerFromContainer(
    new WebAppLoggerContainer($configurator)
);
```

## Logger processors
A container can also implement `LoggerProcessorContainerInterface`, in which case a list of monolog processors
will be injected into logger instance.

