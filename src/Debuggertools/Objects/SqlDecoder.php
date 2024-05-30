<?php

namespace Debuggertools\Objects;



class SqlDecoder
{
    public static function decodeSql($sql): string
    {
        $newSql = $sql;
        $newSql = str_replace(' FROM ', " \n\r    FROM ", $newSql);
        $newSql = str_replace(' INNER  ', " \n\r    INNER ", $newSql);
        return $newSql;
    }
}
