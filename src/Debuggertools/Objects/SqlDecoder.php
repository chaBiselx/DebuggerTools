<?php

namespace Debuggertools\Objects;



class SqlDecoder
{
    public static function decodeSql($sql): string
    {
        $newSql = $sql;
        $newSql = str_replace(' FROM ', " \n    FROM ", $newSql);
        $newSql = str_replace(' CASE ', " \n    CASE ", $newSql);
        $newSql = str_replace(' WHEN ', " \n         WHEN ", $newSql);
        $newSql = str_replace(' ELSE ', " \n         ELSE ", $newSql);
        $newSql = str_replace(' END ', " \n     END ", $newSql);
        $newSql = str_replace(' INNER ', " \n    INNER ", $newSql);
        $newSql = str_replace(' ORDER ', " \n    ORDER ", $newSql);
        $newSql = str_replace(' LIMIT ', " \n    LIMIT ", $newSql);
        return $newSql;
    }
}
