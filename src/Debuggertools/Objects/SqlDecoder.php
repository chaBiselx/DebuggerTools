<?php

namespace Debuggertools\Objects;

use Debuggertools\Interfaces\SqlDecoderInterface;

class SqlDecoder implements SqlDecoderInterface
{
    private $instructionClassique = [
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

    public function decodeSql(string $sql): string
    {
        $newSql = $sql;
        $newSql = $this->decodeString($newSql);
        return $newSql;
    }

    public function decodeString(string $sql)
    {
        $newSql = $sql;
        foreach ($this->instructionClassique as $element) {
            $indent = $this->ident($element['nbGroupSpace']);
            $newSql = str_replace(' ' . $element['instruction'] . ' ', " \n" . $indent . $element['instruction'] . " ", $newSql);
        }
        return $newSql;
    }

    protected function ident(int $nbIndent): string
    {
        $indent = '';
        for ($i = 0; $i <  $nbIndent; $i++) {
            $indent .= '    ';
        }
        return $indent;
    }
}
