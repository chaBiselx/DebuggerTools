<?php

namespace Debuggertools\Objects;



class SqlDecoder
{
    public static function decodeSql($sql): string
    {
        $newSql = $sql;
        $newSql = str_replace(' FROM ', " \n    FROM ", $newSql);
        $newSql = str_replace(' INNER ', " \n    INNER ", $newSql);
        return $newSql;
    }
}
