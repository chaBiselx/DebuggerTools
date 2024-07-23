
Install 
composer require --dev chabiselx/debuggerTools


create a log :
POO
$CustomLog = new CustomLog($ArrayOfOption);
$CustomLog->logger($dataToLog);

Singleton
```
CustomLog::SLogger($dataToLog, ArrayOfOption)
```

list of element : 
- any basic type
- \Doctrine\ORM\QueryBuilder from symfony


create a time logger 
```
$CustomLog = new CustomLog($ArrayOfOption);
$CustomLog->time("label");
sleep(5);
$CustomLog->time("label");
```

