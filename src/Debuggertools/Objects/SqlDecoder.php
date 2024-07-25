<?php

namespace Debuggertools\Objects;

use Debuggertools\Interfaces\SqlDecoderInterface;


class SqlDecoder implements SqlDecoderInterface
{
    private $indent = "    ";

    public function decodeSql(string $sql): string
    {
        $newSql = $sql;
        $newSql = $this->decodeString($newSql);
        return $newSql;
    }

    /**
     * Format a SQL query to make it more readable, including subqueries.
     *
     * @param string $sql The SQL query to format.
     * @param int $level The indentation level for nested subqueries.
     * @return string The formatted SQL query.
     */
    protected function decodeString(string $sql, int $level = 0)
    {
        // Normalize and remove extra spaces for next traitement
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        // Add new lines and indentation for main SQL components
        $sql = preg_replace('/\b(SELECT|FROM|WHERE|GROUP BY|HAVING|ORDER BY|LIMIT|OFFSET|INSERT INTO|VALUES|UPDATE|SET|DELETE FROM|INNER JOIN|LEFT JOIN|RIGHT JOIN|OUTER JOIN)\b/i', "\n$1", $sql);
        $sql = preg_replace('/\b(AND|OR)\b/i', "\n" . str_repeat($this->indent, ($level + 1)) . "$1", $sql);

        // Further indentation for select fields
        $sql = preg_replace('/\bSELECT\b/i', "SELECT\n" . $this->indent, $sql);
        $sql = preg_replace('/,/', ",\n" . str_repeat($this->indent, ($level + 1)) . "", $sql);

        // Handle subqueries by increasing the indentation level
        $sql = preg_replace_callback('/\((SELECT.*?)\)/i', function ($matches) use ($level) {
            return "(\n" . $this->decodeString($matches[1], $level + 1) . "\n" . str_repeat($this->ident, $level) . ")";
        }, $sql);

        // Trim leading and trailing whitespace from each line and add indentation for the current level
        $lines = explode("\n", $sql);
        $formattedSql = '';
        foreach ($lines as $line) {
            $formattedSql .= str_repeat($this->indent, $level) . $line . "\n";
        }

        return $formattedSql;
    }
}
