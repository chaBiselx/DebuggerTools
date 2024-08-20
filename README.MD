# Installation du projet

Présentation général du projet : 

Intallation with composer 
```
composer require --dev chabiselx/debuggerTools
```


## Récupérer le projet 

```
git clone 
```

## Lancer docker

créer le fichier .env.local à la racine du projet

Modifier les port dans le .env.local.env en fonction des autres images docker
- PHP_EXT_PORT_1
- PHP_EXT_PORT_2
- BDD_EXT_PORT


lancer Docker pour la première fois : 
```
docker compose --env-file .env.local up --build --force-recreate -d
```

pour debuguer le build 
```
docker-compose build --no-cache 2>&1 | tee build_logs.txt
``` 

rentrer dans le contanier PHP 
```
docker exec -it debuggertools-php-1 bash
```

# Use case 


create a log :
POO
$Logger = new Logger($ArrayOfOption);
$Logger->logger($dataToLog);

Singleton
```
Logger::loggerStatic($dataToLog, ArrayOfOption)
```

list of element : 
- any basic type
- \Doctrine\ORM\QueryBuilder from symfony


create a time logger 
```
$Logger = new Logger($ArrayOfOption);
$Logger->time("label");
sleep(5);
$Logger->time("label");
```

TODO : 
Make a better README
test Doctrine\ORM\PersistentCollection
test for trace
test for controle data memoryMonitor
get info from curl and generate a command line test
middleware for all input/output
eventListener for Symphony
add curl debug 