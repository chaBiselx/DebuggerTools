
# DebuggerTools

[![Latest Version](https://img.shields.io/github/v/tag/chaBiselx/DebuggerTools?label=version&sort=semver)](https://github.com/chaBiselx/DebuggerTools/releases)
[![License](https://img.shields.io/github/license/chaBiselx/DebuggerTools)](https://github.com/chaBiselx/DebuggerTools/blob/main/LICENSE)

## Description

**DebuggerTools** is a toolkit for developers, designed to facilitate debugging of PHP applications. It offers a set of classes to capture and analyze various aspects of your code execution, including:

- **Logger**: To log messages, errors, and various information.
- **Time Monitor**: To measure and log the execution time of code segments.
- **Memory Monitor**: To measure memory consumption of code segments.
- **Trace**: To trace function calls and follow code execution with fine granularity.

## Table of Contents

- [Installation](#installation)
- [Run Docker for Development](#docker)
- [Testing](#Test)
- [Usage](#usage)
  - [ArrayOfOption](#ArrayOfOption)
  - [Logger](#logger)
  - [Time Monitor](#time-monitor)
  - [Memory Monitor](#memory-monitor)
  - [Trace](#trace)
- [Contribute](#contribute)
- [License](#license)

## Installation

Install DebuggerTools using Composer:

```bash
composer require --dev chabiselx/debuggerTools
```

Or clone the repository directly:

```bash
git clone https://github.com/chaBiselx/DebuggerTools.git
cd DebuggerTools
composer install
```

## Run Docker for Development

Create the `.env.local` file at the project root.

Edit the ports in `.env.local.env` according to other Docker images:
- PHP_EXT_PORT_1
- PHP_EXT_PORT_2
- BDD_EXT_PORT

Run Docker for the first time:
```bash
make docker-build
```

To debug the build if needed:
```bash
docker-compose build --no-cache 2>&1 | tee build_logs.txt
```

Access the PHP container:
```bash
docker exec -it debuggertools-php7_3-1 bash
```

## Testing

### Outside a container:
```bash
make docker-test
```

### Inside a container:
To run all tests:
```bash
make test
```

To run a specific test:
```bash
make test FILTER=file::function
```

## Usage

### ArrayOfOption

The `ArrayOfOption` parameter is an optional list. The possible fields are:

All:

- `hidePrefix` [boolean]: Hide the date at the beginning of the string (default: false)
- `showPrefix` [boolean]: Show the date at the beginning of the string (default: true)
- `purgeFileBefore` [boolean]: Purge the file before writing (default: false)
- `expendObject` [boolean]: Expand for better visibility of arrays in the log file (default: false)
- `fileName` [string]: Write to the file with the given name (default: log)

For the MemoryMonitor class:

- `activeConvertion` [boolean]: Enable unit-to-byte conversion (default: true)
- `disactiveConvertion` [boolean]: Disable unit-to-byte conversion (default: false)

### Logger

To capture logs in your application:

```php
use DebuggerTools\Logger;

$logger = new Logger($ArrayOfOption);
$logger->logger($dataToLog);
```

Via singleton:

```php
\Debuggertools\Logger::loggerStatic($dataToLog, $ArrayOfOption);
```

### Time Monitor

To measure the execution time of a code section:

```php
use DebuggerTools\TimeMonitor;

$time = new TimeMonitor($ArrayOfOption);
$time->set("label");
sleep(5);
$time->log("label");
```

### Memory Monitor

To monitor the memory used by your application:

```php
use DebuggerTools\MemoryMonitor;

$memoryMonitor = new MemoryMonitor($ArrayOfOption);
$memoryMonitor->logMemoryUsage('label');
//function
$memoryMonitor->logMemoryUsage('label');
```

### Trace

To trace code execution:

```php
use DebuggerTools\Trace;

$trace = new Trace($ArrayOfOption);
$trace->logTrace();
```

Via singleton:

```php
\Debuggertools\Trace::getTraceStatic($ArrayOfOption);
```

## Contribute

Contributions are welcome! If you want to contribute:

1. Fork the repository.
2. Create a branch for your changes (`git checkout -b feature/new-feature`).
3. Commit your changes (`git commit -m 'Add a new feature'`).
4. Push the branch (`git push origin feature/new-feature`).
5. Open a Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/chaBiselx/DebuggerTools/blob/main/LICENSE) file for details.

## TODO

- Improve the README
- Test Doctrine\ORM\PersistentCollection
- Middleware for all input/output
- EventListener for Symfony
- Fix SonarLint error (pathLog: getRootPath)
