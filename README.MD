

create a log :
POO
$CustomLog = new CustomLog($ArrayOfOption);
$CustomLog->logger($dateToLog);

static
```
CustomLog::SLogger($dateToLog, ArrayOfOption)
```


create a time logger 
```
$CustomLog = new CustomLog($ArrayOfOption);
$CustomLog->time("label");
sleep(5);
$CustomLog->time("label");
```