<?php

namespace Debuggertools;


class Trace
{

    public function __construct(array $Option = [])
    {
        $Option['hidePrefix'] = true;
        $this->CustomLog = new CustomLog($Option);
    }

    /**
     * get trace
     *
     * @return void
     */
    public function getTrace(): void
    {
        try {
            $texts = $this->generateTrace();
            // write log
            foreach ($texts as $text) {
                $this->CustomLog->logger($text);
            }
        } catch (\Throwable $th) {
            $this->CustomLog->logger(["CUSTOMLOG : an unexpected error has occurred", $th->getMessage(), $th->getTraceAsString()]);
        }
    }

    protected function generateTrace(): array
    {
        $traces = debug_backtrace();
        $basePath = $this->detectBasePathFromTraces($traces);
        $arrayText[] = $basePath;

        $arrayText = [];
        foreach ($traces as $trace) {
            if (preg_match('/chabiselx\/debuggertools/', $trace['file'])) continue;
            if (isset($trace['file'])) {
                $trace['file'] = str_replace($basePath, "", $trace['file']); // remove base path
                $messageFile = $trace['file'] . ' (line : ' . $trace['line'] . ')';
            }
            $messageFunction = "       -> " .  $trace['function'];
            $messageFunction .= "(";
            if ($trace['args'] && !empty($trace['args'])) {
                foreach ($trace['args'] as $k => $arg) {
                    if ($k != 0) $messageFunction .= ' ,';
                    $messageFunction .= $this->convertArgToString($arg);
                }
            }
            $messageFunction .= ")";

            $arrayText[] = $messageFile;
            $arrayText[] = $messageFunction;
        }
        return $arrayText;
    }

    protected function detectBasePathFromTraces($paths)
    {

        if (empty($paths)) {
            return '';
        }

        // Normalize all paths
        $normalizedPaths = array_map('realpath', $paths);

        // Split each path into an array of directories
        $splitPaths = array_map(function ($path) {
            return explode(DIRECTORY_SEPARATOR, $path);
        }, $normalizedPaths);

        // Find the minimum length among the split paths
        $minLength = min(array_map('count', $splitPaths));

        $commonPathParts = [];
        for ($i = 0; $i < $minLength; $i++) {
            $currentPart = $splitPaths[0][$i];
            foreach ($splitPaths as $splitPath) {
                if ($splitPath[$i] !== $currentPart) {
                    return implode(DIRECTORY_SEPARATOR, $commonPathParts);
                }
            }
            $commonPathParts[] = $currentPart;
        }

        return implode(DIRECTORY_SEPARATOR, $commonPathParts);
    }

    protected function convertArgToString($arg): string
    {
        $text = "";
        //check type
        $type = gettype($arg);
        switch ($type) {
            case 'integer':
            case 'float':
            case 'double':
            case 'string':
                $text = $arg;
                break;
            case 'boolean':
                $text = $type . ' : ' . ($arg ? 'TRUE' : 'FALSE');
                break;
            case 'object':
            case 'array':
            default:
                $text = $type;
                break;
        }
        return $text;
    }


    /**
     * static getTrace
     *
     * @param bool|int|float|double|string|array|object|Doctrine\\ORM\\QueryBuilder $data , same data as logger
     * @param  $Option , same data as constructor
     *
     * @return void
     */
    public static function getTraceStatic($Option = []): void
    {
        (new Trace($Option))->getTrace();
    }
}
