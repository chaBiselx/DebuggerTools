<?php

namespace Debuggertools\Objects;



class SqlDecoder
{
    private static $instructionClassique = [
        ['instruction' => 'SELECT', 'nbGroupSpace' => 0],
        ['instruction' => 'FROM', 'nbGroupSpace' => 1],
        ['instruction' => 'CASE', 'nbGroupSpace' => 1],
        ['instruction' => 'WHEN', 'nbGroupSpace' => 2],
        ['instruction' => 'ELSE', 'nbGroupSpace' => 2],
        ['instruction' => 'END', 'nbGroupSpace' => 1],
        ['instruction' => 'INNER', 'nbGroupSpace' => 1],
        ['instruction' => 'LEFT', 'nbGroupSpace' => 1],
        ['instruction' => 'RIGHT', 'nbGroupSpace' => 1],
        ['instruction' => 'OUTER', 'nbGroupSpace' => 1],
        ['instruction' => 'ORDER', 'nbGroupSpace' => 1],
        ['instruction' => 'LIMIT', 'nbGroupSpace' => 1],
        ['instruction' => 'WHERE', 'nbGroupSpace' => 1],
    ];

    public static function decodeSql($sql): string
    {
        $newSql = $sql;
        $newSql = self::decodeString($newSql, self::$instructionClassique);
        return $newSql;
    }

    public static function decodeString(string $sql, array $listElement = [])
    {
        $newSql = $sql;
        foreach ($listElement as $element) {
            $space = '';
            for ($i = 0; $i <  $element['nbGroupSpace']; $i++) {
                $space .= '    ';
            }
            $newSql = str_replace(' ' . $element['instruction'] . ' ', " \n" . $space . $element['instruction'] . " ", $newSql);

            # code...
        }
        return $newSql;
    }
}
