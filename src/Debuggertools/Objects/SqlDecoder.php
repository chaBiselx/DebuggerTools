<?php

namespace Debuggertools\Objects;

use Debuggertools\Interfaces\SqlDecoderInterface;


class SqlDecoder implements SqlDecoderInterface
{
    private $indent = "    ";
    private $SubQuery = [];
    private $indicatorSubQuery = '!#!';

    public function serialize(string $sql): string
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
        $sql = preg_replace('/\b(FROM|WHERE|GROUP BY|HAVING|ORDER BY|LIMIT|OFFSET|INSERT INTO|VALUES|SET|INNER JOIN|LEFT JOIN|RIGHT JOIN|OUTER JOIN)\b/i', "\n$1", $sql);
        $sql = preg_replace('/\b(AND|OR)\b/i', "\n" . str_repeat($this->indent, ($level + 1)) . "$1", $sql);
        $sql = $this->upperCaseSqlComponent($sql);


        // Further indentation for select fields
        if ($level === 0) {
            $sql = preg_replace('/\bSELECT\b/i', "SELECT\n" . $this->indent, $sql);
        }
        $sql = $this->indentForComma($sql, $level);

        //replaces all contents in parentheses with indicators
        $sql = $this->replaceParenthesesWithIndicator($sql);

        //replaces indicators with all contents with parentheses
        $sql = $this->replaceIndicatorWithSubQuery($sql, $level);

        // Trim leading and trailing whitespace from each line and add indentation for the current level
        $lines = explode("\n", $sql);
        $formattedSql = '';
        foreach ($lines as $line) {
            $formattedSql .= str_repeat($this->indent, $level) . $line . "\n";
        }
        $formattedSql = preg_replace('/\s*$/', "", $formattedSql);

        return $formattedSql;
    }

    /**
     * Upper case SQL component
     *
     * @param string $sql
     * @return string
     */
    private function upperCaseSqlComponent(string $sql): string
    {
        return preg_replace_callback('/\b(SELECT|FROM|WHERE|GROUP BY|HAVING|ORDER BY|LIMIT|OFFSET|INSERT INTO|VALUES|UPDATE|SET|DELETE FROM|INNER JOIN|LEFT JOIN|RIGHT JOIN|OUTER JOIN|AND|OR)\b/i', function ($matches) {
            return strtoupper($matches[0]);
        }, $sql);
    }

    /**
     * Endent after comma
     *
     * @param [type] $sql
     * @param [type] $level
     * @return string
     */
    private function indentForComma(string $sql, int $level): string
    {
        return preg_replace('/,(?![^()]*\))/', ",\n" . str_repeat($this->indent, ($level + 1)) . "", $sql);
    }

    /**
     * replaces all contents in parentheses with indicators
     *
     * @param string $sql
     * @return string
     */
    private function replaceParenthesesWithIndicator(string $sql): string
    {
        $matches = [];
        while (preg_match('/\(([^()]+)\)/', $sql, $matches)) {
            $index = count($this->SubQuery);
            $this->SubQuery[$index] = $matches[1];
            $sql = preg_replace('/\(([^()]+)\)/', $this->indicatorSubQuery . $index . $this->indicatorSubQuery, $sql, 1);
            $matches = [];
        }
        return $sql;
    }

    /**
     * replaces indicators with all contents with parentheses
     *
     * @param string $sql
     * @param integer $level
     * @return string
     */
    private function replaceIndicatorWithSubQuery(string $sql, int $level): string
    {
        $matches = [];
        while (preg_match('/' . $this->indicatorSubQuery . '\d*' . $this->indicatorSubQuery . '/', $sql, $matches)) {
            $index = str_replace($this->indicatorSubQuery, '', $matches[0]);
            $subQuery = $this->SubQuery[$index];
            $subQuery = preg_replace('/(^\(|\)$)/i', '', $subQuery); //NOSONARLINT

            if (!preg_match('/\b(SELECT|UPDATE|DELETE)\b/i', $subQuery)) { // function
                $sql = preg_replace(
                    '/' . $this->indicatorSubQuery . '\d*' . $this->indicatorSubQuery . '/',
                    "(" . $subQuery . ")",
                    $sql,
                    1
                );
            } else { //subquery
                $sql = preg_replace(
                    '/' . $this->indicatorSubQuery . '\d*' . $this->indicatorSubQuery . '/',
                    "(\n" . $this->decodeString($subQuery, $level + 1) . "\n)",
                    $sql,
                    1
                );
            }
            $matches = [];
            unset($this->SubQuery[$index]);
        }
        return $sql;
    }
}
