
# DebuggerTools

[![Latest Version](https://img.shields.io/github/v/tag/chaBiselx/DebuggerTools?label=version&sort=semver)](https://github.com/chaBiselx/DebuggerTools/releases)
[![License](https://img.shields.io/github/license/chaBiselx/DebuggerTools)](https://github.com/chaBiselx/DebuggerTools/blob/main/LICENSE)

## Description

**DebuggerTools** est une boîte à outils pour développeurs, conçue pour faciliter le débogage des applications PHP. Elle propose un ensemble de classes permettant de capturer et d'analyser divers aspects de l'exécution de votre code, notamment :

- **Logger** : Pour enregistrer les messages de log, erreurs et informations diverses.
- **Time Monitor** : Pour mesurer et enregistrer le temps d'exécution de segments de code.
- **Memory Monitor** : Pour mesurer la consommation mémoire de segments de code.
- **Trace** : Pour tracer les appels de fonctions et suivre l'exécution du code avec une granularité fine.

## Table des matières

- [Installation](#installation)
- [Lancer docker pour le developpement](#docker)
- [Test](#Test)
- [Utilisation](#utilisation)
  - [ArrayOfOption](#ArrayOfOption)
  - [Logger](#logger)
  - [Time monitor](#time-monitor)
  - [Memory monitor](#memory-monitor)
  - [Trace](#trace)
- [Contribuer](#contribuer)
- [Licence](#licence)

## Installation

Installez DebuggerTools en utilisant Composer :

```bash
composer require --dev chabiselx/debuggerTools
```

Ou clonez directement le dépôt :

```bash
git clone https://github.com/chaBiselx/DebuggerTools.git
cd DebuggerTools
composer install
```

## Lancer docker pour le developpement

créer le fichier .env.local à la racine du projet

Modifier les port dans le .env.local.env en fonction des autres images docker
- PHP_EXT_PORT_1
- PHP_EXT_PORT_2
- BDD_EXT_PORT


lancer Docker pour la première fois : 
```bash
docker compose --env-file .env.local up --build --force-recreate -d
```

pour debuguer le build si besoin
```bash
docker-compose build --no-cache 2>&1 | tee build_logs.txt
``` 

rentrer dans le contanier PHP 
```bash
docker exec -it debuggertools-php-1 bash
```

## Test 

pour executer l'ensempble des tests
```bash
make test
```

pour lancer un test en particulier
```bash
php vendor/bin/phpunit -v --filter file::function
```

## Utilisation

### ArrayOfOption

Le paramètre ArrayOf option est une liste optionnel. 
les different champs possible :

All :

- hidePrefix [boolean] : Hide the date at the beginning of the string (default : false)
- showPrefix [boolean] : Show the date at the beginning of the string (default : true)
- purgeFileBefore [boolean] : purgeFileBefore purge the file before write (default : false)
- expendObject [boolean] : expend for best visibility for array in log file (default : false)
- fileName [string] :  write in the file with the same name (default: log)

Class MemoryMonitor :

- activeConvertion [boolean] : active convert with unit to byte (default: true)
- disactiveConvertion [boolean] : active convert with unit to byte (default: false)


### Logger

Pour capturer des logs dans votre application :

```php
use DebuggerTools\Logger;

$logger = new Logger($ArrayOfOption);
$logger->logger($dataToLog);

```

via singleton 

```php
\Debuggertools\Logger::loggerStatic($dataToLog, $ArrayOfOption);
```

list of element : 
- any basic php type
- \Doctrine\ORM\QueryBuilder from symfony

### Time monitor

Pour mesurer le temps d'exécution d'une section de code :

```php
use DebuggerTools\TimeMonitor;

$time = new TimeMonitor($ArrayOfOption);
$time->set("label");
sleep(5);
$time->log("label");
```

### Memory monitor

Pour surveiller la mémoire utilisée par votre application :

```php
use DebuggerTools\MemoryMonitor;

$memoryMonitor = new MemoryMonitor($ArrayOfOption);
$memoryMonitor->logMemoryUsage('label');
//function
$memoryMonitor->logMemoryUsage('label');
```

### Trace

Pour tracer l'exécution du code :

```php
use DebuggerTools\Trace;

$trace = new Trace($ArrayOfOption);
$trace->logTrace();
```


via singleton 

```php
\Debuggertools\Trace::getTraceStatic($ArrayOfOption);
```

## Contribuer

Les contributions sont les bienvenues ! Si vous souhaitez contribuer :

1. Forkez le dépôt.
2. Créez une branche pour vos modifications (`git checkout -b feature/new-feature`).
3. Committez vos changements (`git commit -m 'Ajouter une nouvelle fonctionnalité'`).
4. Pushez la branche (`git push origin feature/new-feature`).
5. Ouvrez une Pull Request.

## Licence

Ce projet est sous licence MIT. Consultez le fichier [LICENSE](https://github.com/chaBiselx/DebuggerTools/blob/main/LICENSE) pour plus de détails.

## TODO

- Make a better README
- test Doctrine\ORM\PersistentCollection
- get info from curl and generate a command line test
- middleware for all input/output
- eventListener for Symphony
- SonarLint error ( pathLog : getRootPath)